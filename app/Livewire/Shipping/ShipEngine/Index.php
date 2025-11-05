<?php

namespace App\Livewire\Shipping\ShipEngine;

use App\Models\Customer;
use App\Services\ShipEngineService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use TallStackUi\Traits\Interactions;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use Interactions;
    public $shipFromAddress = [
        'name' => '',
        'company_name' => '',
        'phone' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city_locality' => '',
        'state_province' => '',
        'postal_code' => '',
        'country_code' => 'US',
    ];

    public $shipToAddress = [
        'name' => '',
        'company_name' => '',
        'phone' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city_locality' => '',
        'state_province' => '',
        'postal_code' => '',
        'country_code' => 'US',
    ];

    public $package = [
        'weight' => 1,
        'weight_unit' => 'pound',
        'length' => '',
        'width' => '',
        'height' => '',
        'dimension_unit' => 'inch',
        'insured_value' => 1,
        "insurance_provider" => "shipsurance",
    ];

    public $carriers = [];
    public $selectedCarrier = 'se-4038210';
    public $carrierServices = [];
    public $selectedService = '';
    public $rates = [];
    public $selectedRate = null;
    public $trackingNumber = '';
    public $trackingResults = [];
    public $loading = false;
    public $carrierPackaging = [];
    public $selectedPackaging = 'custom';
    public $selectedPackage = null;
    public $isInsuranceChecked = false;

    public function updated($name, $value)
    {
        if ($name === 'isInsuranceChecked') {
            if (!$value) {
                $this->package['insured_value'] = 1;
            }
        }
    }

    public function rules()
    {
        $rules = [
            'shipFromAddress.name' => 'required|string|max:255',
            'shipFromAddress.address_line1' => 'required|string|max:255',
            'shipFromAddress.city_locality' => 'required|string|max:255',
            'shipFromAddress.state_province' => 'required|string|max:2',
            'shipFromAddress.postal_code' => 'required|string|max:10',
            'shipFromAddress.country_code' => 'required|string|size:2',

            'shipToAddress.name' => 'required|string|max:255',
            'shipToAddress.address_line1' => 'required|string|max:255',
            'shipToAddress.city_locality' => 'required|string|max:255',
            'shipToAddress.state_province' => 'required|string|max:2',
            'shipToAddress.postal_code' => 'required|string|max:10',
            'shipToAddress.country_code' => 'required|string|size:2',

            'package.weight' => 'required|numeric|min:0.1',

        ];

        if ($this->selectedPackaging == 'custom') {
            $rules['package.length'] = 'required|numeric|min:1';
            $rules['package.width'] = 'required|numeric|min:1';
            $rules['package.height'] = 'required|numeric|min:1';
        }

        return $rules;
    }

    public function mount()
    {
        $this->loadCarriers();
        $this->setDefaultAddresses();
        $this->loadCarrierPackaging();
    }

    public function render()
    {
        return view('livewire.shipping.shipengine.index');
    }

    public function setDefaultAddresses()
    {
        // Set default ship from address
        $this->shipFromAddress = [
            'name' => Auth::user()->name,
            'company_name' => 'Test Company',
            'phone' =>  Auth::user()->phone,
            'address_line1' =>  Auth::user()->address,
            'address_line2' =>  Auth::user()->address2,
            'city_locality' =>  Auth::user()->city,
            'state_province' => 'FL',
            'postal_code' =>  Auth::user()->zipcode,
            'country_code' => 'US',
        ];

        // Set default ship to address
        $this->shipToAddress = [
            'name' => 'Waleed',
            'company_name' => 'Customer Company',
            'phone' => '555-987-6543',
            'email' => '',
            'address_line1' => '1600 Pennsylvania Ave NW',
            'address_line2' => '',
            'city_locality' => 'Washington',
            'state_province' => 'DC',
            'postal_code' => '20500',
            'country_code' => 'US',
        ];
    }

    public function loadCarriers()
    {
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();
            $response = $shipEngine->getCarriers();
            $this->carriers = $response['carriers'] ?? [];
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load carriers: ' . $e->getMessage())->send();
            Log::error('Failed to load ShipEngine carriers', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
        }
    }

    public function updatedSelectedCarrier()
    {
        if ($this->selectedCarrier) {
            $this->loadCarrierServices();
        } else {
            $this->carrierServices = [];
            $this->selectedService = '';
        }
    }

    public function loadCarrierServices()
    {
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();
            $this->carrierServices = $shipEngine->getCarrierServices($this->selectedCarrier);
            $this->selectedService = '';
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load carrier services: ' . $e->getMessage())->send();
            Log::error('Failed to load ShipEngine carrier services', [
                'carrier_id' => $this->selectedCarrier,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function loadCarrierPackaging()
    {
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();
            $packaging = $shipEngine->getCarrierPackages($this->selectedCarrier);
            $collection = collect($packaging['packages']) ?? collect();

            $customPackaging = [
                'package_id' => 'custom',
                'package_code' => 'custom',
                'name' => 'Custom Box or Rigid Packaging',
                'description' => 'Any custom box or thick parcel',
            ];
            $this->carrierPackaging  = $collection->prepend($customPackaging)->toArray();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load carrier packaging: ' . $e->getMessage())->send();
            Log::error('Failed to load ShipEngine carrier packaging', [
                'carrier_id' => $this->selectedCarrier,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function selectPackaging($package_code)
    {
        $this->selectedPackaging = $package_code;
        $selectedPackage = collect($this->carrierPackaging)->firstWhere('package_code', $package_code);

        $this->selectedPackage = $selectedPackage;
        if ($selectedPackage) {
            $this->toast()->info('Selected packaging: ' . ($selectedPackage['name'] ?? 'Unknown'))->send();
        }
    }

    public function getRates()
    {

        $this->validate();

        $isValidated = $this->validateAddresses();

        if (! $isValidated) {
            return;
        }
        try {
            $this->loading = true;
            $this->rates = [];
            $this->selectedRate = null;

            $shipEngine = new ShipEngineService();


            $packageData = [];

            if ($this->selectedPackage && $this->selectedPackage['package_code'] != 'custom') {
                $packageData =  [
                    'weight' => [
                        'value' => $this->package['weight'] ?? 1,
                        'unit' => $this->package['weight_unit'] ?? 'pound'
                    ],

                    'package_code' => $this->selectedPackage['package_code'] ?? '',

                    'insured_value' => [
                        'amount' => $this->package['insured_value'] ?? 0,
                        'currency' => $this->package['currency'] ?? 'USD'
                    ]
                ];
            } else { // Custom package
                $packageData =  [
                    'weight' => [
                        'value' => $this->package['weight'] ?? 1,
                        'unit' => $this->package['weight_unit'] ?? 'pound'
                    ],
                    'dimensions' => [
                        'length' => $this->package['length'] ?? 12,
                        'width' => $this->package['width'] ?? 12,
                        'height' => $this->package['height'] ?? 12,
                        'unit' => $this->package['dimension_unit'] ?? 'inch'
                    ],
                    'insured_value' => [
                        'amount' => $this->package['insured_value'] ?? 0,
                        'currency' => $this->package['currency'] ?? 'USD'
                    ]
                ];
            }


            $shipmentData = [
                'rate_options' => [
                    'carrier_ids' => ["se-4038210"], // For fedex only
                    'service_codes' => [],
                ],
                'shipment' => [
                    'ship_to' => $shipEngine->formatAddress($this->shipToAddress),
                    'ship_from' => $shipEngine->formatAddress($this->shipFromAddress),
                    "insurance_provider" => "parcelguard",
                    'packages' => [
                        $packageData
                    ],
                ]
            ];

            $response = $shipEngine->getRates($shipmentData);

            if ($response['rate_response']['status'] == 'error') {
                $this->dialog()->error('Error getting rates: ' . ($response['rate_response']['errors'][0]['message'] ?? 'Unknown error'))->send();
                return;
            }


            $responseRates = collect($response['rate_response']['rates'] ?? []);
            if ($responseRates->isEmpty()) {
                $this->dialog()->warning('No rates found for the given shipment details.')->send();
                return;
            }


            $authenticatedUser = Auth::user();
            if ($authenticatedUser instanceof Customer && $authenticatedUser->margin > 0) {
                $formatedRates = array_map(function ($rate) use ($authenticatedUser) {
                    $shippingAmount = (float) $rate['shipping_amount']['amount'];
                    $insuranceAmount = (float) ($rate['insurance_amount']['amount'] ?? 0);
                    $confirmationAmount = (float) ($rate['confirmation_amount']['amount'] ?? 0);
                    $otherAmount = (float) ($rate['other_amount']['amount'] ?? 0);
                    $requestedComparisonAmount = (float) ($rate['requested_comparison_amount']['amount'] ?? 0);
                    $originalTotal = $shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + $requestedComparisonAmount;
                    $marginMultiplier = 1 + ($authenticatedUser->margin / 100);
                    $custmoerMargin = 1 + ($authenticatedUser->customer_margin / 100);
                    $newTotal = $originalTotal * $marginMultiplier * $custmoerMargin;

                    // New data 
                    $rate['original_total'] = $originalTotal;
                    $rate['margin'] = $marginMultiplier;
                    $rate['customer_margin'] = $custmoerMargin;
                    $rate['calculated_amount'] = $newTotal;
                    return $rate;
                }, $responseRates->toArray());
                $this->rates = $formatedRates;
            } else { // WEB Guard
                $this->rates = $responseRates->toArray();
            }


            $this->toast()->success('Rates retrieved successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to get rates: ' . $e->getMessage())->send();
            Log::error('Failed to get ShipEngine rates', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
        }
    }

    public function selectRate($rateId)
    {
        if ($this->selectedRate && $this->selectedRate['rate_id'] == $rateId) {
            $this->selectedRate = null;
            $this->toast()->info('Deselected rate.')->send();
            return;
        }
        $this->selectedRate = collect($this->rates)->firstWhere('rate_id', $rateId);

        $this->toast()->info('Selected rate: ' . ($this->selectedRate['service_type'] ?? 'N/A'))->send();
    }

    public function createLabel()
    {
        if (!$this->selectedRate) {
            $this->toast()->warning('Please select a rate first.')->send();
            return;
        }

        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();

            $labelData = [
                'rate_id' => $this->selectedRate['rate_id'],
                'label_format' => 'pdf',
                'label_layout' => '4x6',
            ];

            $response = $shipEngine->createLabel($labelData);

            $this->toast()->success('Label created successfully! Tracking number: ' . ($response['tracking_number'] ?? 'N/A'))->send();

            // You could download or display the label here
            if (!empty($response['label_download']['pdf'])) {
                $this->dispatch('download-label', $response['label_download']['pdf']);
            }
        } catch (\Exception $e) {
            $this->toast()->error('Failed to create label: ' . $e->getMessage())->send();
            Log::error('Failed to create ShipEngine label', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
        }
    }


    public function trackPackage()
    {
        if (empty($this->trackingNumber)) {
            $this->toast()->warning('Please enter a tracking number.')->send();
            return;
        }

        try {
            $this->loading = true;
            $this->trackingResults = [];

            $shipEngine = new ShipEngineService();
            $response = $shipEngine->trackPackage($this->trackingNumber);

            $this->trackingResults = $response;
            $this->toast()->success('Package tracking information retrieved successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to track package: ' . $e->getMessage())->send();
            Log::error('Failed to track ShipEngine package', [
                'tracking_number' => $this->trackingNumber,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->loading = false;
        }
    }


    public function validateAddresses()
    {
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();

            // Validate ship from address
            $fromValidation = $shipEngine->validateAddress([
                $shipEngine->formatAddress($this->shipFromAddress)
            ]);

            // Validate ship to address
            $toValidation = $shipEngine->validateAddress([
                $shipEngine->formatAddress($this->shipToAddress)
            ]);

            $fromToAddressMessages = $fromValidation[0]['messages'] ?? [];
            $toAddressMessages = $toValidation[0]['messages'] ?? [];
            $fromToAddressMessagesCount = count($fromToAddressMessages);;
            $toAddressMessagesCount = count($toAddressMessages);

            // Check on the ToValidationStatus
            if ($toValidation[0]['status'] == 'error' || $toValidation[0]['status'] == 'unverified') {
                if ($toValidation[0]['messages'][0]['type'] == 'warning') {
                    $this->dialog()->warning('Ship to address ' .  ucfirst($toValidation[0]['messages'][0]['type']) . ": " . $toValidation[0]['messages'][0]['message'])->send();
                } else {
                    $this->dialog()->error('Ship to address ' .  ucfirst($toValidation[0]['messages'][0]['type']) . ": " . $toValidation[0]['messages'][0]['message'])->send();
                }
                return false;
            }

            // Check on the FromValidationStatus
            if ($fromValidation[0]['status'] == 'error' || $fromValidation[0]['status'] == 'unverified') {
                if ($fromValidation[0]['messages'][0]['type'] == 'warning') {
                    $this->dialog()->warning('Ship from address ' .  ucfirst($fromValidation[0]['messages'][0]['type']) . ": " . $fromValidation[0]['messages'][0]['message'])->send();
                } else {
                    $this->dialog()->error('Ship from address ' .  ucfirst($fromValidation[0]['messages'][0]['type']) . ": " . $fromValidation[0]['messages'][0]['message'])->send();
                }

                return false;
            }

            // Check on the messages count if there return false and show messages
            if ($fromToAddressMessagesCount > 0) {
                $messagesText = '';
                foreach ($fromToAddressMessages as $message) {
                    $messagesText .= $message['message'] . ' ';
                }
                $this->dialog()->warning('Ship from address has issues: ' . trim($messagesText))->send();
                return false;
            }

            if ($toAddressMessagesCount > 0) {
                $messagesText = '';
                foreach ($toAddressMessages as $message) {
                    $messagesText .= $message['message'] . ' ';
                }
                $this->dialog()->warning('Ship to address has issues: ' . trim($messagesText))->send();
                return false;
            }


            $successMessage = 'Addresses verified successfully!';
            $this->toast()->success($successMessage)->send();
            return true;
        } catch (\Exception $e) {
            $this->toast()->error('Failed to validate addresses: ' . $e->getMessage())->send();
            Log::error('Failed to validate ShipEngine addresses', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
        }
    }
}
