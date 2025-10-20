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
        'postalCode' => '65247',
        'countryCode' => 'US',
        'stateOrProvinceCode' => '',
        'city' => '',
        'streetLines' => ['']
    ];

    public array $recipient = [
        'postalCode' => '75063',
        'countryCode' => 'US',
        'stateOrProvinceCode' => '',
        'city' => '',
        'streetLines' => [''],
        'residential' => false
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
                'length' => 10,
                'width' => 10,
                'height' => 10,
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
            $this->shipper['postalCode'] = $authenticatedUser->zipcode ?? '';
            $this->shipper['city'] = $authenticatedUser->city ?? '';
            $this->shipper['stateOrProvinceCode'] = $authenticatedUser->state ?? '';
            $this->shipper['streetLines'] = [$authenticatedUser->address ?? ''];
        } elseif ($authenticatedUser instanceof User) {
            $this->shipper['postalCode'] = $authenticatedUser->zipcode ?? '';
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

    public function getFedExQuote()
    {
        $this->validate([
            'shipper.postalCode' => 'required|string',
            'shipper.countryCode' => 'required|string',
            'recipient.postalCode' => 'required|string',
            'recipient.countryCode' => 'required|string',
            'requestedPackageLineItems.*.weight.value' => 'required|numeric|min:0.1',
        ]);
        try {
            // Get access token
            $accessToken = $this->getFedExAccessToken();

            // Prepare payload - simplified to match required structure only
            $payload = [
                'accountNumber' => [
                    'value' => $this->accountNumber
                ],
                'requestedShipment' => [
                    'shipper' => [
                        'address' => [
                            'postalCode' => $this->shipper['postalCode'],
                            'countryCode' => $this->shipper['countryCode']
                        ]
                    ],
                    'recipient' => [
                        'address' => [
                            'postalCode' => $this->recipient['postalCode'],
                            'countryCode' => $this->recipient['countryCode']
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
                                'value' => $package['weight']['value']
                            ]
                        ];
                    }, $this->requestedPackageLineItems)
                ]
            ];

            // Make API request
            $ratesUrl = config('fedex.sandbox_mode') ?
                config('fedex.urls.sandbox.rates') :
                config('fedex.urls.production.rates');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'X-locale' => 'en_US'
            ])->post($ratesUrl, $payload);


            info($response->json());

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['output']['rateReplyDetails'])) {
                    $this->quotes = $responseData['output']['rateReplyDetails'];
                    $this->hasResponse = true;
                    $this->errorMessage = '';
                } else {
                    $this->errorMessage = 'No quotes found in response';
                    $this->hasResponse = true;
                }
            } else {
                $errorData = $response->json();
                $errorMessage = 'FedEx API Error: ';

                if (isset($errorData['errors'][0])) {
                    $error = $errorData['errors'][0];
                    $code = $error['code'] ?? '';
                    $message = $error['message'] ?? 'Unknown error';

                    // Provide helpful messages for common errors
                    if ($code === 'SHIPPER.POSTALSTATE.MISMATCH') {
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
