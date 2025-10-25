<?php

namespace App\Livewire\Shipping\Fedex;

use App\Models\Country;
use App\Models\Customer;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    // FedEx API Credentials
    public string $clientId;
    public string $clientSecret;
    public string $accountNumber;

    // Form data matching FedEx API structure
    public array $shipper = [
        'postalCode' => 65247, // Memphis, TN - FedEx headquarters area
        'countryCode' => 'US',
        'stateOrProvinceCode' => 'TN',
        'city' => 'Collierville',
        'streetLines' => ['1000 FedEx Dr']
    ];

    public array $recipient = [
        // FedEx API required fields
        'postalCode' => 75063, // Beverly Hills, CA - well-known valid postal code
        'countryCode' => 'US',
        'stateOrProvinceCode' => 'CA',
        'city' => 'Beverly Hills',
        'streetLines' => ['123 Main St'],
        'residential' => false,

        // Additional form fields matching shipping/index
        'email' => '',
        'phone' => '',
        'name' => '',
        'company' => '',
        'address' => '',
        'apt' => '',
        'state' => 'CA',
        'country' => 'US'
    ];

    public string $pickupType = 'DROPOFF_AT_FEDEX_LOCATION';
    public string $serviceType = 'FEDEX_2_DAY';
    public array $rateRequestType = ['PREFERRED'];
    public string $preferredCurrency = 'USD';

    public array $requestedPackageLineItems = [
        [
            'weight' => [
                'units' => 'LB',
                'value' => 1
            ],
            'dimensions' => [
                'length' => null,
                'width' => null,
                'height' => null,
                'units' => 'IN'
            ]
        ]
    ];

    // Response data
    public array $quotes = [];
    public string $errorMessage = '';
    public bool $hasResponse = false;
    public ?string $accessToken = null;

    public function mount()
    {
        // Load credentials from config
        $this->clientId = config('fedex.client_id');
        $this->clientSecret = config('fedex.client_secret');
        $this->accountNumber = config('fedex.account_number');

        // Set default values from config
        $this->pickupType = config('fedex.defaults.pickup_type');
        $this->serviceType = config('fedex.defaults.service_type');
        $this->rateRequestType = config('fedex.defaults.rate_request_type');
        $this->preferredCurrency = config('fedex.defaults.currency');

        // Set shipper zipcode from authenticated user
        $authenticatedUser = Auth::user();
        if ($authenticatedUser instanceof Customer) {
            // $this->shipper['postalCode'] = $authenticatedUser->zipcode ?? '';
            $this->shipper['city'] = $authenticatedUser->city ?? '';
            $this->shipper['stateOrProvinceCode'] = $authenticatedUser->state ?? '';
            $this->shipper['streetLines'] = [$authenticatedUser->address ?? ''];
        } elseif ($authenticatedUser instanceof User) {
            // $this->shipper['postalCode'] = $authenticatedUser->zipcode ?? '';
            $this->shipper['city'] = $authenticatedUser->city ?? '';
            $this->shipper['stateOrProvinceCode'] = $authenticatedUser->state ?? '';
            $this->shipper['streetLines'] = [$authenticatedUser->address ?? ''];
        }
    }

    public function addPackage()
    {
        $this->requestedPackageLineItems[] = [
            'weight' => [
                'units' => 'LB',
                'value' => 1
            ],
            'dimensions' => [
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'units' => 'IN'
            ]
        ];
    }

    public function removePackage($index)
    {
        if (count($this->requestedPackageLineItems) > 1) {
            unset($this->requestedPackageLineItems[$index]);
            $this->requestedPackageLineItems = array_values($this->requestedPackageLineItems);
        }
    }

    private function getFedExAccessToken()
    {
        // Check if token exists in cache
        $token = Cache::get('fedex_access_token');
        if ($token) {
            return $token;
        }

        try {
            $authUrl = config('fedex.sandbox_mode') ?
                config('fedex.urls.sandbox.auth') :
                config('fedex.urls.production.auth');


            $response = Http::asForm()->post($authUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials'
            ]);


            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;

                // Cache token for slightly less than expiry time
                Cache::put('fedex_access_token', $accessToken, now()->addSeconds($expiresIn - 60));

                return $accessToken;
            } else {
                throw new \Exception('Failed to get FedEx access token: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('FedEx authentication error: ' . $e->getMessage());
        }
    }

    /**
     * Sync form fields with API fields before making request
     */
    private function syncRecipientFields()
    {
        // Sync country code for API
        $this->recipient['countryCode'] = $this->recipient['country'] ?: $this->recipient['countryCode'];

        // Sync other address fields
        $this->recipient['city'] = $this->recipient['city'];
        $this->recipient['stateOrProvinceCode'] = $this->recipient['state'];

        // Combine address and apt into streetLines
        $streetLines = array_filter([
            $this->recipient['address'],
            $this->recipient['apt']
        ]);
        $this->recipient['streetLines'] = $streetLines ?: [''];
    }

    public function getFedExQuote()
    {
        $this->validate([
            'shipper.postalCode' => 'required',
            'recipient.email' => 'nullable|email',
            'recipient.name' => 'required|string',
            'recipient.address' => 'required|string',
            'recipient.city' => 'required|string',
            'recipient.state' => 'required|string',
            'recipient.postalCode' => 'required',
            'recipient.country' => 'required|string',
            'requestedPackageLineItems.*.weight.value' => 'required|numeric|min:0.1',
            'requestedPackageLineItems.*.dimensions.length' => 'required|integer|min:1|max:999',
            'requestedPackageLineItems.*.dimensions.width' => 'required|integer|min:1|max:999',
            'requestedPackageLineItems.*.dimensions.height' => 'required|integer|min:1|max:999',
        ]);

        // Sync form fields with API fields
        $this->syncRecipientFields();

        try {
            // Get access token
            $accessToken = $this->getFedExAccessToken();

            info('Access Token:', ['access_token' => $accessToken]);
            // Prepare payload - enhanced with all required fields
            $payload = [
                'accountNumber' => [
                    'value' => $this->accountNumber
                ],
                'requestedShipment' => [
                    'shipper' => [
                        'address' => [
                            'postalCode' => $this->shipper['postalCode'],
                            'countryCode' => $this->shipper['countryCode'],
                            // 'stateOrProvinceCode' => $this->shipper['stateOrProvinceCode'] ?: null,
                            // 'city' => $this->shipper['city'] ?: null,
                            // 'streetLines' => array_filter($this->shipper['streetLines'])
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'postalCode' => $this->recipient['postalCode'],
                            'countryCode' => $this->recipient['countryCode'],
                            // 'stateOrProvinceCode' => $this->recipient['stateOrProvinceCode'] ?: null,
                            // 'city' => $this->recipient['city'] ?: null,
                            // 'streetLines' => array_filter($this->recipient['streetLines']),
                            // 'residential' => $this->recipient['residential']
                        ]
                    ],
                    'pickupType' => $this->pickupType,
                    // 'serviceType' => $this->serviceType,
                    'rateRequestType' => $this->rateRequestType,
                    'preferredCurrency' => $this->preferredCurrency,
                    'requestedPackageLineItems' => array_map(function ($package) {
                        return [
                            'weight' => [
                                'units' => $package['weight']['units'],
                                'value' => floatval($package['weight']['value'])
                            ],
                            'dimensions' => [
                                'length' => (int)$package['dimensions']['length'],
                                'width' => (int)$package['dimensions']['width'],
                                'height' => (int)$package['dimensions']['height'],
                                'units' => 'IN'
                            ],
                            'declaredValue' => [
                                'currency' => $this->preferredCurrency,
                                'amount' => 100
                            ]
                        ];
                    }, $this->requestedPackageLineItems)
                ]
            ];

            // Remove null values to clean up the payload
            $payload = $this->removeNullValues($payload);


            // Make API request
            $ratesUrl = config('fedex.sandbox_mode') ?
                config('fedex.urls.sandbox.rates') :
                config('fedex.urls.production.rates');

            // Make API request with retry for SYSTEM.UNAVAILABLE.EXCEPTION
            $maxRetries = 3;
            $retryCount = 0;
            $response = null;

            do {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'X-locale' => 'en_US'
                ])->post($ratesUrl, $payload);

                // Check for system unavailable and retry
                if (
                    $response->status() === 503 ||
                    (isset($response->json()['errors'][0]['code']) &&
                        $response->json()['errors'][0]['code'] === 'SYSTEM.UNAVAILABLE.EXCEPTION')
                ) {
                    $retryCount++;
                    if ($retryCount < $maxRetries) {
                        sleep(2); // Wait 2 seconds before retry
                        continue;
                    }
                }
                break;
            } while ($retryCount < $maxRetries);

            // Log response for debugging
   
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['output']['rateReplyDetails'])) {
                    $quotes = $responseData['output']['rateReplyDetails'];

                    $authenticatedUser = Auth::user();
                    if ($authenticatedUser instanceof Customer && $authenticatedUser->margin > 0) {
                        $quotes = array_map(function ($quote) use ($authenticatedUser) {
                            info("========================= Single Quotes =====================");
                            info($quote);
                            info("========================= Single Quotes =====================");
                            $originalTotal = (float) $quote['ratedShipmentDetails'][0]['totalNetCharge'];
                            $marginMultiplier = 1 + ($authenticatedUser->margin / 100);
                            $newTotal = $originalTotal * $marginMultiplier;
                            $quote['ratedShipmentDetails'][0]['totalNetCharge'] = number_format($newTotal, 2, '.', '');
                            return $quote;
                        }, $quotes);
                    }

                    // Sort quotes by totalAmount from lowest to highest
                    usort($quotes, function ($a, $b) {
                        return (float) $a['ratedShipmentDetails'][0]['totalNetCharge'] <=> (float) $b['ratedShipmentDetails'][0]['totalNetCharge'];
                    });

                    $this->quotes = $quotes;

                    $this->hasResponse = true;
                    $this->errorMessage = '';
                } else {
                    $this->errorMessage = 'No quotes found in response';
                    $this->hasResponse = true;
                }
            } else {
                $errorData = $response->json();

                // Log full error response for debugging
                info('FedEx API Error Response:', $errorData);

                $errorMessage = 'FedEx API Error: ';

                if (isset($errorData['errors'][0])) {
                    $error = $errorData['errors'][0];
                    $code = $error['code'] ?? '';
                    $message = $error['message'] ?? 'Unknown error';

                    // Handle SYSTEM.UNAVAILABLE.EXCEPTION specifically
                    if ($code === 'SYSTEM.UNAVAILABLE.EXCEPTION') {
                        $errorMessage = 'FedEx service is temporarily unavailable. This is usually a temporary issue with the FedEx sandbox/test environment. Please try again in a few moments, or contact support if the issue persists.';
                    } elseif ($code === 'SHIPPER.POSTALSTATE.MISMATCH') {
                        $errorMessage = 'Shipper postal code and state do not match. Please verify your address details.';
                    } elseif ($code === 'RECIPIENT.POSTALSTATE.MISMATCH') {
                        $errorMessage = 'Recipient postal code and state do not match. Please verify the recipient address.';
                    } elseif (str_contains($code, 'POSTAL')) {
                        $errorMessage = 'Address validation error: ' . $message . '. Please check all postal codes and states.';
                    } else {
                        $errorMessage .= $message . ' (Code: ' . $code . ')';
                    }
                } else {
                    $errorMessage .= 'Unknown error occurred';
                }

                $this->errorMessage = $errorMessage;
                $this->hasResponse = true;
            }
        } catch (\Exception $e) {
            // Log the exception details
            info('FedEx Exception:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->hasResponse = true;
        }
    }

    #[Computed(persist: true)]
    public function pickupTypes()
    {
        $types = config('fedex.pickup_types');
        return collect($types)->map(function ($label, $value) {
            return ['value' => $value, 'label' => $label];
        })->values()->toArray();
    }

    #[Computed(persist: true)]
    public function serviceTypes()
    {
        $types = config('fedex.service_types');
        return collect($types)->map(function ($label, $value) {
            return ['value' => $value, 'label' => $label];
        })->values()->toArray();
    }

    #[Computed(persist: true)]
    public function countries()
    {
        return Country::select(['id', 'label', 'value'])
            ->where('status', true)
            ->orderBy('label')
            ->get();
    }

    #[Computed(persist: true)]
    public function currencies()
    {
        return [
            ['value' => 'USD', 'label' => 'US Dollar'],
            ['value' => 'CAD', 'label' => 'Canadian Dollar'],
            ['value' => 'EUR', 'label' => 'Euro'],
            ['value' => 'GBP', 'label' => 'British Pound']
        ];
    }

    /**
     * Remove null values from array recursively
     */
    private function removeNullValues($array)
    {
        return array_filter(array_map(function ($value) {
            return is_array($value) ? $this->removeNullValues($value) : $value;
        }, $array), function ($value) {
            return !is_null($value) && $value !== '';
        });
    }

    private function validateUSPostalCode($postalCode, $state, $addressType = 'address')
    {
        // Basic US postal code validation
        if (!preg_match('/^\d{5}(-\d{4})?$/', $postalCode)) {
            $this->addError("{$addressType}.postalCode", 'Invalid US postal code format. Use 12345 or 12345-6789.');
            return;
        }

        // Get first 3 digits for state validation
        $zipPrefix = substr($postalCode, 0, 3);

        // Basic state-to-zip validation (simplified - covers major ranges)
        $stateZipRanges = [
            'AL' => ['350-369'],
            'AK' => ['995-999'],
            'AZ' => ['850-860'],
            'AR' => ['716-729'],
            'CA' => ['900-961'],
            'CO' => ['800-816'],
            'CT' => ['060-069'],
            'DE' => ['197-199'],
            'FL' => ['320-349'],
            'GA' => ['300-319'],
            'HI' => ['967-968'],
            'ID' => ['832-838'],
            'IL' => ['600-629'],
            'IN' => ['460-479'],
            'IA' => ['500-528'],
            'KS' => ['660-679'],
            'KY' => ['400-427'],
            'LA' => ['700-714'],
            'ME' => ['039-049'],
            'MD' => ['206-219'],
            'MA' => ['010-027'],
            'MI' => ['480-499'],
            'MN' => ['550-567'],
            'MS' => ['386-397'],
            'MO' => ['630-658'],
            'MT' => ['590-599'],
            'NE' => ['680-693'],
            'NV' => ['889-898'],
            'NH' => ['030-038'],
            'NJ' => ['070-089'],
            'NM' => ['870-884'],
            'NY' => ['100-149'],
            'NC' => ['270-289'],
            'ND' => ['580-588'],
            'OH' => ['430-458'],
            'OK' => ['730-749'],
            'OR' => ['970-979'],
            'PA' => ['150-196'],
            'RI' => ['028-029'],
            'SC' => ['290-299'],
            'SD' => ['570-577'],
            'TN' => ['370-385'],
            'TX' => ['750-799'],
            'UT' => ['840-847'],
            'VT' => ['050-059'],
            'VA' => ['220-246'],
            'WA' => ['980-994'],
            'WV' => ['247-268'],
            'WI' => ['530-549'],
            'WY' => ['820-831'],
            'DC' => ['200-205']
        ];

        if (isset($stateZipRanges[$state])) {
            $zipNum = (int)$zipPrefix;
            $isValid = false;

            foreach ($stateZipRanges[$state] as $range) {
                if (strpos($range, '-') !== false) {
                    [$min, $max] = explode('-', $range);
                    if ($zipNum >= (int)$min && $zipNum <= (int)$max) {
                        $isValid = true;
                        break;
                    }
                } else {
                    if ($zipNum == (int)$range) {
                        $isValid = true;
                        break;
                    }
                }
            }

            if (!$isValid) {
                $this->addError("{$addressType}.postalCode", "Postal code {$postalCode} does not match state {$state}. Please verify your address.");
            }
        }
    }

    public function render()
    {
        return view('livewire.shipping.fedex.index');
    }
}
