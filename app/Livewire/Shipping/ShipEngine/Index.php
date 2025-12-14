<?php

namespace App\Livewire\Shipping\ShipEngine;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Shipment;
use App\Services\ShipEngineService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    use Interactions;

    public $end_user_total, $customer_total, $origin_total;
    public $total_weight = 0; // Total weight of all packages in pounds
    public $shipFromAddress = [
        'name' => '',
        'company_name' => '',
        'phone' => '',
        'email' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city_locality' => '',
        'state_province' => '',
        'postal_code' => '',
        'country_code' => 'US',
        'address_residential_indicator' => false
    ];

    public $shipToAddress = [
        'name' => '',
        'company_name' => '',
        'phone' => '',
        'email' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city_locality' => '',
        'state_province' => '',
        'postal_code' => '',
        'country_code' => 'US',
        'address_residential_indicator' => true
    ];
    public $shipToAddressCountryFullName = '';
    public $hsipFromAddressCountryFullName = '';

    public $packages = [];
    public $currentPackageIndex = 0; // Track which package is being edited

    public $customs = [
        'contents' => 'merchandise', // [merchandise, documents]
        'signer' => '',
        'non_delivery' => 'return_to_sender',
        'terms_of_trade_code' => 'DDU',
        'declaration' => 'I hereby certify that the information on this invoice is true and correct and the contents and value of this shipment is as stated above.',
        'customs_items' => [
            // [
            //     "description" => "Wooden coffee table",
            //     "quantity" => 1,
            //     "value" => [
            //         "currency" => "usd",
            //         "amount" => 150.00
            //     ],
            //     "harmonized_tariff_code" => "940360",
            //     "country_of_origin" => "US",
            //     "weight" => [
            //         "value" => 12,
            //         "unit" => "pound"
            //     ]
            // ]
            [
                "description" => "",
                "quantity" => '',
                "value" => [
                    "currency" => "usd",
                    "amount" => ''
                ],
                "harmonized_tariff_code" => "",
                "country_of_origin" => "",
                "weight" => [
                    "value" => '',
                    "unit" => "pound"
                ]
            ]
        ]
    ];

    public $tax_identifiers = [
        [
            "taxable_entity_type" => "shipper",
            // "identifier_type" => "eori",
            "value" => "",
            "issuing_authority" => ""
        ],
        [
            "taxable_entity_type" => "recipient",
            // "identifier_type" => "eori",
            "value" => "",
            "issuing_authority" => ""
        ]
    ];

    public $shipDate  = null;

    public $carriers = [];
    public $selectedCarrier = 'se-4121981';
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
    public $lastRequestData = null; // Store the last request sent to ShipEngine API
    public $lastCreatedShipment = null; // Store the shipment record created from API response

    // Sorting properties
    public $sortBy = 'price'; // 'price' or 'delivery'
    public $sortDirection = 'asc'; // 'asc' or 'desc'

    public $showModal = false;
    public $signature;

    // Payment-related properties
    public $showPaymentModal = false;
    public $paymentProcessing = false;
    public $paymentRetryCount = 0;
    public $maxRetryAttempts = 3;
    public $paymentIntentId = null;
    public $paymentSuccessful = false;
    public $paymentError = null;
    public $availableReaders = [];
    public $selectedReaderId = null;

    // Certification checkboxes
    public $certifyHazardousMaterials = false;
    public $certifyInvoiceAccuracy = false;

    public $packagingAmount = 0;
    public $taxAmount = 0;

    public $doortag_price = 0;



    public function updated($name, $value)
    {
        // Handle insurance checkbox changes for current package
        if (str_starts_with($name, 'packages.') && str_contains($name, '.is_insured')) {
            preg_match('/packages\.(\d+)\.is_insured/', $name, $matches);
            if (isset($matches[1])) {
                $index = (int)$matches[1];
                if (!$value && isset($this->packages[$index])) {
                    $this->packages[$index]['insured_value'] = 100;
                }
            }
        }

        // Recalculate total weight when any package weight changes
        if (str_starts_with($name, 'packages.') && str_contains($name, '.weight')) {
            $this->calculateTotalWeight();
        }

        // For the ship date, ensure the format is correct
        if ($name === 'shipDate') {
            try {
                // Try both formats
                $this->shipDate = \Carbon\Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->shipDate = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
            }
        }

        if ($name === 'shipToAddress.country_code') {
            $country = Country::where('value', $value)->first();
            $this->shipToAddressCountryFullName = $country ? $country->label : '';
        }

        // Update the taxAmount based on the packagingAmount and user's tax percentage
        if ($name === 'packagingAmount') {
            $user = Auth::user();
            $taxPercentage = $user->tax_percentage ?? 0;
            $this->taxAmount = round(($value * $taxPercentage) / 100, 2);
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
            'shipToAddress.country_code' => 'required|string|size:2',

            'packages' => 'required|array|min:1',
        ];

        // Validate each package
        foreach ($this->packages as $index => $package) {
            $rules["packages.{$index}.weight"] = 'required|numeric|min:0.1';
            $rules["packages.{$index}.insured_value"] = 'required_if:packages.' . $index . '.is_insured,true|numeric|min:100';

            // Validate dimensions for custom packages
            if (isset($package['package_code']) && $package['package_code'] === 'custom') {
                $rules["packages.{$index}.length"] = 'required|numeric|min:1';
                $rules["packages.{$index}.width"] = 'required|numeric|min:1';
                $rules["packages.{$index}.height"] = 'required|numeric|min:1';
            }
        }

        if ($this->shipToAddress['country_code'] == 'US') {
            $rules['shipToAddress.state_province'] = 'required|string|max:2';
            $rules['shipToAddress.postal_code'] = 'required|string|max:20';
        } else {
            $rules['shipToAddress.state_province'] = 'nullable|string|max:255';
            $rules['shipToAddress.postal_code'] = 'nullable|string|max:20';
        }

        // Add customs validation for international shipments
        if ($this->shipToAddress['country_code'] != 'US') {
            $rules['customs.contents'] = 'required|string';
            $rules['customs.non_delivery'] = 'required|string';
            $rules['customs.customs_items'] = 'required|array|min:1';

            // Validate each customs item
            foreach ($this->customs['customs_items'] as $index => $item) {
                $rules["customs.customs_items.{$index}.description"] = 'required|string|max:255';
                $rules["customs.customs_items.{$index}.quantity"] = 'required|integer|min:1';
                $rules["customs.customs_items.{$index}.value.amount"] = 'required|numeric|min:0.01';
                $rules["customs.customs_items.{$index}.harmonized_tariff_code"] = 'required|string|max:20';
                $rules["customs.customs_items.{$index}.country_of_origin"] = 'required|string|size:2';
                $rules["customs.customs_items.{$index}.weight.value"] = 'required|numeric|min:0.01';
                // customs.content is documents then harmonized_tariff_code is not required
                if ($this->customs['contents'] == 'documents') {
                    $rules["customs.customs_items.{$index}.harmonized_tariff_code"] = 'nullable|string|max:20';
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        foreach ($this->packages as $index => $package) {
            $messages["packages.{$index}.insured_value.min"] = 'Package ' . ($index + 1) . ': Please enter an amount of $101 or more. Amounts below this are already covered by the carrier.';
            $messages["packages.{$index}.weight.required"] = 'Package ' . ($index + 1) . ': Weight is required.';
            $messages["packages.{$index}.weight.min"] = 'Package ' . ($index + 1) . ': Weight must be at least 0.1 lbs.';
        }

        return $messages;
    }

    public function mount()
    {
        $this->loadCarriers();
        $this->setDefaultAddresses();
        $this->loadCarrierPackaging();
        $this->setDefaultSelectedPackage();
        $this->initializePackages();

        // Set a default date for today date
        $this->shipDate = now()->format('Y-m-d');

        $country = Country::where('value', $this->shipToAddress['country_code'])->first();
        $this->shipToAddressCountryFullName = $country ? $country->label : '';
    }

    public function initializePackages()
    {
        // Initialize with one default package
        if (empty($this->packages)) {
            $this->packages = [
                [
                    'weight' => '',
                    'weight_unit' => 'pound',
                    'length' => '',
                    'width' => '',
                    'height' => '',
                    'dimension_unit' => 'inch',
                    'insured_value' => 100,
                    'is_insured' => false,
                    'package_code' => $this->selectedPackaging ?? 'custom',
                ]
            ];
        }
    }

    public function addPackage()
    {
        $this->packages[] = [
            'weight' => '',
            'weight_unit' => 'pound',
            'length' => '',
            'width' => '',
            'height' => '',
            'dimension_unit' => 'inch',
            'insured_value' => 100,
            'is_insured' => false,
            'package_code' => $this->selectedPackaging ?? 'custom',
        ];

        $this->toast()->success('Package added successfully!')->send();
    }

    public function removePackage($index)
    {
        if (count($this->packages) <= 1) {
            $this->toast()->warning('At least one package is required.')->send();
            return;
        }

        if (isset($this->packages[$index])) {
            array_splice($this->packages, $index, 1);
            $this->packages = array_values($this->packages); // Re-index array
            $this->calculateTotalWeight(); // Recalculate total weight after removing package
            $this->toast()->info('Package removed successfully.')->send();
        }
    }

    /**
     * Calculate the total weight of all packages
     */
    public function calculateTotalWeight()
    {
        $this->total_weight = 0;

        foreach ($this->packages as $package) {
            if (!empty($package['weight'])) {
                $this->total_weight += ceil((float) $package['weight']);
            }
        }
    }

    public function updatePackageCode($index)
    {
        if (isset($this->packages[$index])) {
            $this->packages[$index]['package_code'] = $this->selectedPackaging;
            $this->toast()->info('Package type updated for Package ' . ($index + 1))->send();
        }
    }

    public function selectPackagingForPackage($packageIndex, $packageCode)
    {
        if (isset($this->packages[$packageIndex])) {
            $this->packages[$packageIndex]['package_code'] = $packageCode;

            // Find the package details
            $selectedPackage = collect($this->carrierPackaging)->firstWhere('package_code', $packageCode);

            if ($selectedPackage) {
                $this->toast()->info('Package ' . ($packageIndex + 1) . ': Selected ' . $selectedPackage['name'])->send();
            }
        }
    }

    public function setDefaultSelectedPackage()
    {
        // Set default selected package to 'custom'
        if (!empty($this->carrierPackaging)) {
            $defaultPackage = collect($this->carrierPackaging)->firstWhere('package_code', $this->selectedPackaging);
            $this->selectedPackage = $defaultPackage;
        }

        // Always ensure selectedPackage has a fallback value
        if (empty($this->selectedPackage)) {
            $this->selectedPackage = [
                'package_id' => 'custom',
                'package_code' => 'custom',
                'name' => 'Custom Box or Rigid Packaging',
                'description' => 'Any custom box or thick parcel',
            ];
        }
    }

    public function setDefaultAddresses()
    {
        $authenticatedUser = Auth::user();
        $canModifyData = $authenticatedUser->can_modify_data ?? true;

        // Set default ship from address
        if ($canModifyData) {
            // If user can modify data, start with empty fields (like shipTo)
            $this->shipFromAddress = [
                'name' => '',
                'company_name' => '',
                'phone' => '',
                'email' => '',
                'address_line1' => '',
                'address_line2' => '',
                'city_locality' => '',
                'state_province' => '',
                'postal_code' => '',
                'country_code' => 'US',
                'address_residential_indicator' => false
            ];
        } else {
            // If user cannot modify data, populate with their stored data (read-only)
            $this->shipFromAddress = [
                'name' => '',
                'company_name' => 'DoorTag',
                'phone' => $authenticatedUser->phone,
                'email' => $authenticatedUser->email,
                'address_line1' => $authenticatedUser->address,
                'address_line2' => $authenticatedUser->address2,
                'city_locality' => $authenticatedUser->city,
                'state_province' => $authenticatedUser->state,
                'postal_code' => $authenticatedUser->zipcode,
                'country_code' => 'US',
                'address_residential_indicator' => $authenticatedUser->address_residential_indicator
            ];
        }

        // $this->shipFromAddress = [
        //     'name' => '',
        //     'company_name' => '',
        //     'phone' =>  '',
        //     'email' =>  '',
        //     'address_line1' =>  '',
        //     'address_line2' =>  '',
        //     'city_locality' =>  '',
        //     'state_province' => '',
        //     'postal_code' =>  '',
        //     'country_code' => 'US',
        //     'address_residential_indicator' => false
        // ];

        // Set default ship to address
        $this->shipToAddress = [
            'name' => '',
            'company_name' => '',
            'phone' => '',
            'email' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city_locality' => '',
            "state_province" => "",
            'postal_code' => '',
            'country_code' => 'US',
            'address_residential_indicator' => false
        ];
        // $this->shipToAddress = [
        //     'name' => '',
        //     'company_name' => '',
        //     'phone' => '',
        //     'email' => '',
        //     // 'address_line1' => '',
        //     // 'address_line1' => '1600 Amphitheatre Pkwy',
        //     "address_line1" => "Röntgenstr. 3",
        //     'address_line2' => '',
        //     // 'city_locality' => '',
        //     // 'city_locality' => 'mountain view',
        //     "city_locality" => "Esslingen am Neckar",
        //     // 'state_province' => 'CA',
        //     "state_province" => "",
        //     // 'postal_code' => '',
        //     // 'postal_code' => '94043',
        //     "postal_code" => "73730",
        //     // 'country_code' => 'US',
        //     "country_code" => "DE",
        //     'address_residential_indicator' => true
        // ];
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

            $filteredPackagest = $collection->filter(function ($package) {
                // Exclude pakages which name includes "One"
                return !str_contains(strtolower($package['name']), 'one');
            });

            $customPackaging = [
                'package_id' => 'custom',
                'package_code' => 'custom',
                'name' => 'Custom Box or Rigid Packaging',
                'description' => 'Any custom box or thick parcel',
            ];
            $this->carrierPackaging  = $filteredPackagest->prepend($customPackaging)->toArray();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load carrier packaging: ' . $e->getMessage())->send();
        } finally {
            $this->loading = false;
        }
    }

    public function selectPackaging($package_code)
    {
        $this->selectedPackaging = $package_code;
        $selectedPackage = collect($this->carrierPackaging)->firstWhere('package_code', $package_code);

        $this->selectedPackage = $selectedPackage;
        if ($this->selectedPackage) {
            $this->toast()->info('Selected packaging: ' . ($this->selectedPackage['name'] ?? 'Unknown'))->send();
        }
    }

    public function addCustomsItem()
    {
        $this->customs['customs_items'][] = [
            "description" => "",
            "quantity" => '',
            "value" => [
                "currency" => "usd",
                "amount" => ''
            ],
            "harmonized_tariff_code" => "",
            "country_of_origin" => "",
            "weight" => [
                "value" => '',
                "unit" => "pound"
            ]
        ];

        $this->toast()->info('Added new customs item')->send();
    }

    public function removeCustomsItem($index)
    {
        // Ensure we don't remove the last item (minimum 1 required)
        $currentCount = count($this->customs['customs_items']);

        if ($currentCount > 1 && isset($this->customs['customs_items'][$index])) {
            // Create a new array without the specified index
            $newItems = [];
            foreach ($this->customs['customs_items'] as $i => $item) {
                if ($i !== (int)$index) {
                    $newItems[] = $item;
                }
            }

            // Update the array
            $this->customs['customs_items'] = $newItems;

            $this->toast()->info('Removed customs item ' . ($index + 1))->send();
        } else if ($currentCount <= 1) {
            $this->toast()->warning('At least one customs item is required')->send();
        } else {
            $this->toast()->error('Could not find item to remove')->send();
        }
    }

    public function getRates()
    {

        $this->validate(
            $this->rules(),
            $this->messages()
        );

        // Calculate total weight of all packages
        $this->calculateTotalWeight();

        // handle the address_residential_indicator
        $this->shipFromAddress['address_residential_indicator'] = $this->shipFromAddress['address_residential_indicator'] == true ? 'yes' : 'no';
        $this->shipToAddress['address_residential_indicator'] = $this->shipToAddress['address_residential_indicator'] == true ? 'yes' : 'no';


        if ($this->shipToAddress['country_code'] == "US") {
            $isValidated = $this->validateAddresses();
            if (! $isValidated) {
                return;
            }
        }

        try {
            $this->loading = true;
            $this->rates = [];
            $this->selectedRate = null;

            $shipEngine = new ShipEngineService();

            // Build packages array
            $packagesData = [];
            $hasAnyInsurance = false;

            foreach ($this->packages as $package) {
                $packageData = [];

                // Check if package is custom or predefined
                if (isset($package['package_code']) && $package['package_code'] !== 'custom') {
                    $packageData = [
                        'weight' => [
                            'value' => $package['weight'] ?? 1,
                            'unit' => $package['weight_unit'] ?? 'pound'
                        ],
                        'package_code' => $package['package_code'],
                    ];
                } else {
                    // Custom package - include dimensions
                    $packageData = [
                        'weight' => [
                            'value' => ceil((float) ($package['weight'] ?? 1)),
                            'unit' => $package['weight_unit'] ?? 'pound'
                        ],
                        'dimensions' => [
                            'length' => ceil((float) ($package['length'] ?? 12)),
                            'width' => ceil((float) ($package['width'] ?? 12)),
                            'height' => ceil((float) ($package['height'] ?? 12)),
                            'unit' => $package['dimension_unit'] ?? 'inch'
                        ],
                    ];
                }

                // Add insurance if checked for this package
                if (isset($package['is_insured']) && $package['is_insured']) {
                    $packageData['insured_value'] = [
                        'amount' => $package['insured_value'] ?? 100,
                        'currency' => 'USD'
                    ];
                    $hasAnyInsurance = true;
                }

                $packagesData[] = $packageData;
            }

            $shipmentData = [
                'rate_options' => [
                    'carrier_ids' => ["se-4121981", "se-4084605"], // For fedex only
                    'service_codes' => [],
                ],
                'shipment' => [
                    'ship_date' => $this->shipDate, // Format: YYYY-MM-DD
                    'ship_to' => $shipEngine->formatAddress($this->shipToAddress),
                    'ship_from' => $shipEngine->formatAddress($this->shipFromAddress),
                    'packages' => $packagesData,
                ]
            ];

            // Add insurance provider if any package has insurance
            if ($hasAnyInsurance) {
                $shipmentData['shipment']['insurance_provider'] = 'parcelguard';
            }

            // ================== Just For International ==================
            if ($this->shipToAddress['country_code'] != 'US') {
                // Modify the issuing_authority based on country codes
                foreach ($this->tax_identifiers as $key => $identifier) {
                    if ($identifier['taxable_entity_type'] == 'shipper') {
                        $this->tax_identifiers[$key]['issuing_authority'] = $this->shipFromAddress['country_code'];
                    } elseif ($identifier['taxable_entity_type'] == 'recipient') {
                        $this->tax_identifiers[$key]['issuing_authority'] = $this->shipToAddress['country_code'];
                    }
                }

                $shipmentData['shipment']['customs'] = $this->customs;
                $shipmentData['shipment']['tax_identifiers'] = $this->tax_identifiers;
            } else {
                // Remove customs for domestic shipments
                if (isset($shipmentData['shipment']['customs'])) {
                    unset($shipmentData['shipment']['customs']);
                }
                // Remove tax_identifiers for domestic shipments
                if (isset($shipmentData['shipment']['tax_identifiers'])) {
                    unset($shipmentData['shipment']['tax_identifiers']);
                }
            }

            // Store the request data for later use

            $response = $shipEngine->getRates($shipmentData);



            // store the $shipToAddressCountryFullName in the $shipmentData
            $shipmentData['ship_to_address_country_full_name'] = $this->shipToAddressCountryFullName;
            $this->lastRequestData = $shipmentData;

            if ($response['rate_response']['status'] == 'error') {
                $this->dialog()->error('Error getting rates: ' . ($response['rate_response']['errors'][0]['message'] ?? 'Unknown error'))->send();
                return;
            }


            $responseRates = collect($response['rate_response']['rates'] ?? []);
            if ($responseRates->isEmpty()) {
                $this->dialog()->warning('No rates found for the given shipment details.')->send();
                return;
            }

            // excludes the rates those have service_code as 'fedex_first_overnight'
            $responseRates = $responseRates->filter(function ($rate) {
                return $rate['service_code'] !== 'fedex_first_overnight';
            });


            $authenticatedUser = Auth::user();
            if ($authenticatedUser instanceof Customer && $authenticatedUser->margin > 0) {
                $formatedRates = array_map(function ($rate) use ($authenticatedUser) {
                    $shippingAmount = (float) $rate['shipping_amount']['amount'];
                    $insuranceAmount = (float) ($rate['insurance_amount']['amount'] ?? 0);
                    $confirmationAmount = (float) ($rate['confirmation_amount']['amount'] ?? 0);
                    $otherAmount = (float) ($rate['other_amount']['amount'] ?? 0);
                    $requestedComparisonAmount = (float) ($rate['requested_comparison_amount']['amount'] ?? 0);
                    $originalTotal = $shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + $requestedComparisonAmount;
                    $marginMultiplier = 1 - ($authenticatedUser->margin / 100);
                    $custmoerMargin = 1 - ($authenticatedUser->customer_margin / 100);
                    $newTotal = $originalTotal * $marginMultiplier * $custmoerMargin;

                    // Set Data to stored in the shipments table
                    $this->origin_total = number_format($originalTotal, 2); // Doortag 100
                    $this->customer_total = number_format($originalTotal * $marginMultiplier, 2); // apnabazar 120
                    $this->end_user_total = number_format($newTotal, 2);  // end user 140

                    // New data 
                    $rate['original_total'] = number_format($originalTotal, 2);
                    $rate['margin'] = number_format($marginMultiplier, 2);
                    $rate['customer_margin'] = number_format($custmoerMargin, 2);
                    $rate['customer_total'] = number_format((float)$this->customer_total, 2);
                    $rate['calculated_amount'] = number_format($newTotal, 2);
                    // if ($authenticatedUser->is_admin) {
                    //     $rate['calculated_amount'] = number_format((float)$this->customer_total, 2);
                    // } else {
                    //     $rate['calculated_amount'] = number_format((float)$this->end_user_total, 2);
                    // }
                    return $rate;
                }, $responseRates->toArray());
            } else { // WEB Guard
                $formatedRates = array_map(function ($rate) use ($authenticatedUser) {
                    $shippingAmount = (float) $rate['shipping_amount']['amount'];
                    $insuranceAmount = (float) ($rate['insurance_amount']['amount'] ?? 0);
                    $confirmationAmount = (float) ($rate['confirmation_amount']['amount'] ?? 0);
                    $otherAmount = (float) ($rate['other_amount']['amount'] ?? 0);
                    $requestedComparisonAmount = (float) ($rate['requested_comparison_amount']['amount'] ?? 0);
                    $originalTotal = $shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + $requestedComparisonAmount;

                    // Set Data to stored in the shipments table
                    $this->origin_total = number_format($originalTotal, 2);


                    // $this->customer_total = number_format($originalTotal * $marginMultiplier, 2);
                    // $this->end_user_total = number_format($newTotal, 2);

                    // New data 
                    $rate['original_total'] = number_format($originalTotal, 2);
                    $rate['calculated_amount'] = number_format($originalTotal, 2);
                    return $rate;
                }, $responseRates->toArray());
            }


            // Add price comparison between carriers
            $this->rates = $this->addPriceComparison($formatedRates);

            $this->sortRates(); // Apply default sorting
            $this->dispatch('scroll-to-top');
            $this->toast()->success('Rates retrieved successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to get rates: ' . $e->getMessage())->send();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Add price comparison between carriers for the same service type
     * Groups rates by service type and compares prices between se-4121981 and se-4084605
     * 
     * @param array $formatedRates
     * @return array Rates with price_comparison added
     */
    private function addPriceComparison($formattedRates)
    {
        $ratesCollection = collect($formattedRates);
        // Group by service type → then key by carrier_id
        $ratesByService = $ratesCollection
            ->groupBy('service_type')
            ->map(fn($serviceRates) => $serviceRates->keyBy('carrier_id'));

        return collect($formattedRates)->map(function ($rate) use ($ratesByService) {

            $serviceType = $rate['service_type'];
            $serviceRates = $ratesByService->get($serviceType, collect());

            // Target carriers
            $carrier1 = 'se-4121981';
            $carrier2 = 'se-4084605';

            // Default comparison structure
            $rate['price_comparison'] = [
                'carrier_1_id'         => $carrier1,
                'carrier_2_id'         => $carrier2,
                'carrier_1_price'      => null,
                'carrier_2_price'      => null,
                'price_difference'     => null,
                'difference_percentage' => null,
                'is_cheaper'           => null,
            ];

            $carrier1Rate = $serviceRates->get($carrier1);
            $carrier2Rate = $serviceRates->get($carrier2);

            if ($carrier1Rate && $carrier2Rate) {

                // Convert to float
                $price1 = (float) str_replace(',', '', $carrier1Rate['calculated_amount']);
                $price2 = (float) str_replace(',', '', $carrier2Rate['calculated_amount']);

                $rate['price_comparison']['carrier_1_price'] = $price1;
                $rate['price_comparison']['carrier_2_price'] = $price2;

                // Absolute difference
                $difference = abs($price1 - $price2);
                $rate['price_comparison']['price_difference'] = $difference;

                // Correct percentage based on highest price
                $base = max($price1, $price2);
                $percentage = ($difference / $base) * 100;
                $rate['price_comparison']['difference_percentage'] = round($percentage, 2);

                // Determine which is cheaper
                $rate['price_comparison']['is_cheaper'] =
                    $price1 < $price2 ? 'carrier_1' : ($price2 < $price1 ? 'carrier_2' : 'equal');
            }

            return $rate;
        })->values()->all();
    }

    public function selectRate($rateId)
    {
        if ($this->selectedRate && $this->selectedRate['rate_id'] == $rateId) {
            $this->selectedRate = null;
            $this->toast()->info('Deselected rate.')->send();

            // Set default prices for the [origin_total, customer_total, end_user_total]

            if (Auth::user() instanceof Customer) {
                $this->customer_total = null;
                $this->end_user_total = null;
            } else {
                $this->customer_total = null;
            }


            return;
        }
        $this->selectedRate = collect($this->rates)->firstWhere('rate_id', $rateId);
        $this->toast()->info('Selected rate: ' . ($this->selectedRate['service_type'] ?? 'N/A'))->send();

        $shippingAmount = (float) $this->selectedRate['shipping_amount']['amount'];
        $insuranceAmount = (float) ($this->selectedRate['insurance_amount']['amount'] ?? 0);
        $confirmationAmount = (float) ($this->selectedRate['confirmation_amount']['amount'] ?? 0);
        $otherAmount = (float) ($this->selectedRate['other_amount']['amount'] ?? 0);
        $requestedComparisonAmount = (float) ($this->selectedRate['requested_comparison_amount']['amount'] ?? 0);
        $originalTotal = $shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + $requestedComparisonAmount;

        $authenticatedUser = Auth::user();
        if ($authenticatedUser instanceof Customer && $authenticatedUser->margin > 0) {
            $marginMultiplier = 1 - ($authenticatedUser->margin / 100);
            $custmoerMargin = 1 - ($authenticatedUser->customer_margin / 100);
            $newTotal =  $originalTotal * $marginMultiplier * $custmoerMargin;

            // Set Data to stored in the shipments table
            $this->origin_total = number_format($originalTotal, 2);
            $this->customer_total = number_format($originalTotal * $marginMultiplier, 2);
            $this->end_user_total = number_format($newTotal, 2);
        } else {

            // Set Data to stored in the shipments table
            $this->origin_total = number_format($originalTotal, 2);
        }

        // Clear customer_total and end_user_total for non-customer users
        if (!($authenticatedUser instanceof Customer)) {
            $this->customer_total = null;
            $this->end_user_total = null;
        }

        // Store the doortag Price
        $this->doortag_price = $this->selectedRate['price_comparison']['carrier_1_price'] ?? null;
    }

    public function sortByPrice()
    {
        if ($this->sortBy === 'price') {
            // Toggle direction if already sorting by price
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set to price sorting with ascending (cheapest first)
            $this->sortBy = 'price';
            $this->sortDirection = 'asc';
        }

        $this->sortRates();

        $direction = $this->sortDirection === 'asc' ? 'lowest to highest' : 'highest to lowest';
        $this->toast()->info("Sorted by price: {$direction}")->send();
    }

    public function sortByDelivery()
    {
        if ($this->sortBy === 'delivery') {
            // Toggle direction if already sorting by delivery
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set to delivery sorting with ascending (closest first)
            $this->sortBy = 'delivery';
            $this->sortDirection = 'asc';
        }

        $this->sortRates();

        $direction = $this->sortDirection === 'asc' ? 'earliest to latest' : 'latest to earliest';
        $this->toast()->info("Sorted by delivery: {$direction}")->send();
    }

    private function sortRates()
    {
        if (empty($this->rates)) {
            return;
        }

        $rates = collect($this->rates);

        if ($this->sortBy === 'price') {
            $rates = $rates->sortBy(function ($rate) {
                // Remove formatting and convert to float for proper numeric sorting
                $amount = str_replace(',', '', $rate['calculated_amount']);
                return (float) $amount;
            });
        } elseif ($this->sortBy === 'delivery') {
            $rates = $rates->sortBy(function ($rate) {
                if (!isset($rate['estimated_delivery_date'])) {
                    return PHP_INT_MAX; // Put rates without delivery date at the end
                }

                try {
                    $deliveryDate = Carbon::parse($rate['estimated_delivery_date']);
                    return $deliveryDate->timestamp;
                } catch (\Exception $e) {
                    return PHP_INT_MAX; // Put invalid dates at the end
                }
            });
        }

        // Apply direction
        if ($this->sortDirection === 'desc') {
            $rates = $rates->reverse();
        }

        $this->rates = $rates->values()->all();

        // info("Final Rates" . print_r($this->rates, true));
    }




    /**
     * Save base64 signature as PNG file in public storage
     * 
     * @param string $base64Signature
     * @return string|null Path to saved signature file
     */
    private function saveSignature($base64Signature)
    {
        try {
            // Remove the data:image/png;base64, prefix if present
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Signature);

            // Decode base64
            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                Log::error('Failed to decode base64 signature');
                return null;
            }

            // Generate unique filename
            $filename = 'signatures/' . date('Y/m/d') . '/' . Str::uuid() . '.png';

            // Store in public disk
            $stored = Storage::disk('public')->put($filename, $imageData);

            if ($stored) {
                return $filename;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error saving signature: ' . $e->getMessage());
            return null;
        }
    }

    public function createLabel()
    {
        // Validate signature and certifications
        $this->validate([
            'signature' => 'nullable|string',
            'certifyHazardousMaterials' => 'accepted',
            'certifyInvoiceAccuracy' => $this->shipToAddress['country_code'] != 'US' ? 'accepted' : 'nullable',
        ], [
            'signature.required' => 'Please provide a signature before creating the label.',
            'certifyHazardousMaterials.accepted' => 'You must certify that the shipment does not contain hazardous materials.',
            'certifyInvoiceAccuracy.accepted' => 'You must certify the accuracy of the invoice information.',
        ]);

        if (!$this->selectedRate) {
            $this->toast()->warning('Please select a rate first.')->send();
            return;
        }

        // Close the shipment details modal
        $this->showModal = false;

        // NEW FLOW: Charge customer directly with saved card
        $this->processCustomerPayment();

        // OLD FLOW: Terminal payment (commented out but kept for reference)
        // $this->showPaymentModal();
    }

    /**
     * NEW: Process payment using customer's saved card
     */
    public function processCustomerPayment()
    {
        try {
            // Get authenticated customer
            $customer = Auth::guard('customer')->user();

            // If no customer or no stripe_customer_id, fall back to admin flow (web auth)
            if (!$customer) {
                $customer = Auth::guard('web')->user();
            }

            if (!$customer || !$customer->stripe_customer_id) {
                $this->toast()->error('No payment method found. Please contact support to add a payment method to your account.')->send();
                Log::error('Customer payment failed: No stripe_customer_id', ['user_id' => $customer->id ?? null]);
                return;
            }

            $totalAmount = ($this->selectedRate['calculated_amount'] + ($this->packagingAmount ?? 0) + ($this->taxAmount ?? 0)) * 100; // in cents

            // Prepare payment data
            $paymentData = [
                'amount' => (int) round($totalAmount),
                'customer_id' => $customer->stripe_customer_id,
                'description' => 'Shipping Label - ' . ($this->selectedRate['service_type'] ?? 'Unknown'),
                'service_type' => $this->selectedRate['service_type'] ?? '',
                'carrier' => $this->selectedRate['carrier_code'] ?? '',
            ];


            // Call the TerminalController directly
            $terminalController = app(\App\Http\Controllers\TerminalController::class);
            $request = request()->merge($paymentData);

            $result = $terminalController->chargeCustomer($request);
            $resultData = $result->getData(true);

            if (isset($resultData['success']) && $resultData['success'] && $resultData['status'] === 'succeeded') {
                // Payment successful, create the label
                $this->createActualLabel($resultData['payment_intent']);

                $this->toast()->success('Payment processed successfully!')->send();
            } else {
                $errorMsg = 'Payment failed with status: ' . ($resultData['status'] ?? 'unknown');
                Log::error('Customer payment failed', ['response' => $resultData]);
                $this->toast()->error($errorMsg)->send();
            }
        } catch (\Exception $e) {
            Log::error('Customer payment exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->toast()->error('Payment error: ' . $e->getMessage())->send();
        }
    }



    public function createActualLabel($paymentIntentData)
    {
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();

            $response = $shipEngine->createLabel($this->selectedRate['rate_id']);

            // Check if the response contains API errors
            if (isset($response['status']) && $response['status'] === 'error') {
                if (isset($response['api_errors']) && !empty($response['api_errors'])) {
                    $errorMessage = $response['api_errors'][0]['message'] ?? 'Unknown API error';
                    $this->dialog()->error($errorMessage)->send();
                } else {
                    $this->dialog()->error('An unknown error occurred while creating the label.')->send();
                }
                return;
            }

            if ($response['status'] == 'completed') {
                // Save signature as PNG file
                if ($this->signature) {
                    $signaturePath = "storage/" . $this->saveSignature($this->signature);
                } else {
                    $signaturePath = null;
                }

                // Calculate total with packaging for storage
                $totalWithPackaging = 0;
                if (auth('customer')->check()) {
                    $totalWithPackaging = (float) str_replace(',', '', $this->end_user_total ?? 0) + ($this->packagingAmount ?? 0);
                } else {
                    $totalWithPackaging = (float) str_replace(',', '', $this->origin_total ?? 0) + ($this->packagingAmount ?? 0);
                }

                $shipmentRecord = Shipment::create([
                    'label_id' => $response['label_id'] ?? null,
                    'user_id' => auth('web')->check() ? auth('web')->id() : null,
                    'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
                    'shipment_data' => json_encode($response),
                    'request_data' => json_encode($this->lastRequestData),
                    'ship_from' => json_encode($response['ship_from'] ?? []),
                    'signature_path' =>  $signaturePath,
                    'origin_total' => $this->origin_total,
                    'customer_total' => $this->customer_total ?? null,
                    'end_user_total' => $this->end_user_total ?? null,
                    // Store packaging information
                    'packaging_amount' => $this->packagingAmount ?? 0,
                    'tax_amount' => $this->taxAmount ?? 0,
                    'total_with_packaging' => $totalWithPackaging,
                    // Store shipment weight information
                    'total_weight' => $this->total_weight,
                    // Store Stripe payment information
                    'stripe_response' => $paymentIntentData,
                    'stripe_payment_intent_id' => $this->paymentIntentId,
                    'stripe_amount_paid' => $paymentIntentData['amount'] / 100,
                    'stripe_payment_status' => $paymentIntentData['status'],
                    'carrier_delivery_days' => $this->selectedRate['carrier_delivery_days'] ?? null,
                    'estimated_delivery_date' => $this->selectedRate['estimated_delivery_date'] ?? null,
                    'door_tag_price' => $this->doortag_price ?? null,
                ]);

                // Store the shipment record for downloadPDF to use
                $this->lastCreatedShipment = $shipmentRecord;

                // Close payment modal and reset data
                $this->showPaymentModal = false;
                $this->resetData();

                $successMessage = 'Payment successful! Label created successfully!';
                $trackingNumber = '';
                if (isset($response['tracking_number'])) {
                    $trackingNumber = $response['tracking_number'];
                    $successMessage .= ' Tracking number: ' . $trackingNumber;
                }

                // Add download buttons for available formats
                $downloadButtons = '';

                // Add PDF Shipment Details button - pass the shipment record
                $printReceiptButton = '
                <div class="mt-4">
                <a  
                    href="' . route('shipments.receipt', $response['label_id']) . '" target="_blank"
                class="block w-full items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" title="Download Shipment Details PDF">
                <i class="fas fa-file-download mr-1"></i>
                Print Receipt
                </a>
                    </div>
                ';

                // Check for PNG label
                if (isset($response['label_download']['png'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['label_download']['png'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="View PNG Label"><i class="fas fa-image mr-1"></i>Label PNG</button>';
                }

                // Check for PDF label
                if (isset($response['label_download']['pdf'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['label_download']['pdf'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="View PDF Label"><i class="fas fa-file-pdf mr-1"></i>Label PDF</button>';
                }

                // Check for Form download
                if (isset($response['form_download']['href'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['form_download']['href'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" title="View Customs Form"><i class="fas fa-file-alt mr-1"></i>Form</button>';
                }

                if (!empty($downloadButtons)) {
                    $successMessage .= $printReceiptButton . '<div class="flex items-center justify-between mt-3">' . $downloadButtons . '</div>';
                }

                $this->dialog()->success($successMessage)->send();
            }
        } catch (\Exception $e) {
            $this->dialog()->error('Failed to create label after successful payment: ' . $e->getMessage())->send();
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
        } finally {
            $this->loading = false;
        }
    }


    public function resetData()
    {
        $this->rates = [];
        $this->selectedRate = null;
        $this->trackingResults = [];
        $this->signature = null;

        // Reset payment-related properties
        $this->showPaymentModal = false;
        $this->paymentProcessing = false;
        $this->paymentRetryCount = 0;
        $this->paymentIntentId = null;
        $this->paymentSuccessful = false;
        $this->paymentError = null;
        $this->availableReaders = [];
        $this->selectedReaderId = null;

        // Reset certification checkboxes
        $this->certifyHazardousMaterials = false;
        $this->certifyInvoiceAccuracy = false;

        // Reset packages to single default package
        $this->packages = [
            [
                'weight' => '',
                'weight_unit' => 'pound',
                'length' => '',
                'width' => '',
                'height' => '',
                'dimension_unit' => 'inch',
                'insured_value' => 100,
                'is_insured' => false,
                'package_code' => $this->selectedPackaging ?? 'custom',
            ]
        ];

        $this->shipFromAddress = [
            'name' => '',
            'company_name' => '',
            'phone' => '',
            'email' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city_locality' => '',
            'state_province' => '',
            'postal_code' => '',
            'country_code' => 'US',
            'address_residential_indicator' => false
        ];

        $this->shipToAddress = [
            'name' => '',
            'company_name' => '',
            'phone' => '',
            'email' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city_locality' => '',
            'state_province' => '',
            'postal_code' => '',
            'country_code' => 'US',
            'address_residential_indicator' => true
        ];
    }


    public function backToCreateRatesPage()
    {
        $this->rates = [];
        $this->selectedRate = null;
    }

    public function downloadPDF($trackingNumber = null, $signaturePath = null)
    {
        try {
            $shipment = $this->lastCreatedShipment;

            if (!$shipment) {
                Log::error('No shipment data available for PDF download');
                $this->dialog()->error('Shipment data not available.')->send();
                return;
            }

            // Prefer request_data (which contains all original data) over response data
            if ($shipment->request_data) {
                $requestData = json_decode($shipment->request_data, true);
                // Extract from nested shipment structure
                $shipmentData = is_array($requestData['shipment'] ?? null) ? $requestData['shipment'] : [];
            } else {
                // Fallback to response data for older records
                $shipmentData = json_decode($shipment->shipment_data, true);
            }

            if (!is_array($shipmentData)) {
                Log::error('Invalid shipment_data: ' . ($shipment->shipment_data ?? $shipment->request_data));
                $this->dialog()->error('Invalid shipment data.')->send();
                return;
            }

            // Get tracking number from response data
            $trackingResponse = json_decode($shipment->shipment_data, true);
            $tracking = $trackingResponse['tracking_number'] ?? $trackingNumber;

            $logoPath = public_path('assets/images/logo-black.png');
            $logoBase64 = '';

            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $signatureBase64 = '';
            if ($shipment->signature_path) {
                $signatureFilePath = $shipment->signature_path;
                $fullSignaturePath = $signatureFilePath;

                // Handle different path formats - signature_path stored as "storage/signatures/2025/11/16/uuid.png"
                // Files are actually in storage/app/public/signatures/
                if (strpos($signatureFilePath, 'storage/') === 0) {
                    // Remove 'storage/' prefix and look in storage/app/public
                    $cleanPath = str_replace('storage/', '', $signatureFilePath);
                    $fullSignaturePath = storage_path('app/public/' . $cleanPath);
                } elseif (strpos($signatureFilePath, 'signatures/') === 0) {
                    // Path is relative to storage/app/public
                    $fullSignaturePath = storage_path('app/public/' . $signatureFilePath);
                } else {
                    // Assume it's relative to storage/app/public
                    $fullSignaturePath = storage_path('app/public/' . $signatureFilePath);
                }

                if (file_exists($fullSignaturePath)) {
                    $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($fullSignaturePath));
                } else {
                    Log::warning('Signature file not found in downloadPDF: ' . $fullSignaturePath . ' (original: ' . $signatureFilePath . ')');
                }
            }

            // Extract shipment details from request data (which has all the data we sent)
            $shipFromAddress = is_array($shipmentData['ship_from'] ?? null) ? $shipmentData['ship_from'] : [];
            $shipToAddress = is_array($shipmentData['ship_to'] ?? null) ? $shipmentData['ship_to'] : [];
            $customs = is_array($shipmentData['customs'] ?? null) ? $shipmentData['customs'] : [];
            $packages = is_array($shipmentData['packages'] ?? null) ? $shipmentData['packages'] : [];

            // Get the first package or create empty one
            $firstPackage = is_array($packages[0] ?? null) ? $packages[0] : [];

            // Get tax identifiers from request data
            $taxIdentifiers = is_array($shipmentData['tax_identifiers'] ?? null) ? $shipmentData['tax_identifiers'] : [];

            // Get service details from response data (shipment_data)
            $serviceCode = $trackingResponse['service_code'] ?? '';
            $carrierCode = $trackingResponse['carrier_code'] ?? '';

            $data = [
                'shipFromAddress' => $shipFromAddress,
                'shipToAddress' => $shipToAddress,
                'package' => [
                    'weight' => is_array($firstPackage['weight'] ?? null) ? $firstPackage['weight']['value'] ?? 0 : 0,
                    'weight_unit' => is_array($firstPackage['weight'] ?? null) ? $firstPackage['weight']['unit'] ?? 'pound' : 'pound',
                    'length' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['length'] ?? null : null,
                    'width' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['width'] ?? null : null,
                    'height' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['height'] ?? null : null,
                    'dimension_unit' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['unit'] ?? 'inch' : 'inch',
                    'insured_value' => is_array($firstPackage['insured_value'] ?? null) ? $firstPackage['insured_value']['amount'] ?? 0 : 0,
                ],
                'customs' => $customs,
                'tax_identifiers' => $taxIdentifiers,
                'shipDate' => $shipmentData['ship_date'] ?? null,
                'serviceCode' => $serviceCode,
                'carrierCode' => $carrierCode,
                'selectedRate' => [
                    'service_type' => $serviceCode,
                    'carrier_code' => $carrierCode,
                    'estimated_delivery_date' => $trackingResponse['estimated_delivery_date'] ?? null,
                    'calculated_amount' => $shipment->end_user_total ?? $shipment->customer_total ?? $shipment->origin_total ?? 0,
                ],
                'carrierPackaging' => [
                    [
                        'package_code' => $firstPackage['package_code'] ?? 'package',
                        'name' => ucfirst(str_replace('_', ' ', $firstPackage['package_code'] ?? 'Package'))
                    ]
                ],
                'selectedPackaging' => $firstPackage['package_code'] ?? 'package',
                'selectedPackage' => [
                    'package_code' => $firstPackage['package_code'] ?? 'package',
                    'name' => ucfirst(str_replace('_', ' ', $firstPackage['package_code'] ?? 'Package'))
                ],
                'isInsuranceChecked' => (is_array($firstPackage['insured_value'] ?? null) ? $firstPackage['insured_value']['amount'] ?? 0 : 0) > 0,
                'end_user_total' => $shipment->end_user_total,
                'customer_total' => $shipment->customer_total,
                'origin_total' => $shipment->origin_total,
                'logoBase64' => $logoBase64,
                'trackingNumber' => $tracking,
                'signatureBase64' => $signatureBase64,
                'ship_to_address_country_full_name' => $requestData['ship_to_address_country_full_name'] ?? ($shipToAddress['country_code'] ?? ''),
                'orderNumber' => $shipment->id,
                'paymentNumber' => $shipment->stripe_payment_intent_id,
                'stripe_amount_paid' => $shipment->stripe_amount_paid,
                'packaging_amount' => $shipment->packaging_amount,
                'tax_amount' => $shipment->tax_amount,
                'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
            ];

            $pdf = Pdf::loadView('pdfs.shipment-details', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10);

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'shipment-details-' . now()->format('Y-m-d-His') . '.pdf'
            );
        } catch (\Exception $e) {
            Log::error('ShipEngine PDF Download Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dialog()->error('Failed to generate PDF: ' . $e->getMessage())->send();
        }
    }

    #[Computed()]
    public function countries()
    {
        return Country::where('status', 1)->orderBy('label')->get();
    }

    #[Computed()]
    public function certificationsCompleted()
    {
        $hazardousCompleted = $this->certifyHazardousMaterials;
        $invoiceCompleted = $this->shipToAddress['country_code'] != 'US' ? $this->certifyInvoiceAccuracy : true;

        return $hazardousCompleted && $invoiceCompleted;
    }

    #[Computed()]
    public function userCanModifyData()
    {
        return Auth::user()->can_modify_data ?? true;
    }

    public function render()
    {
        return view('livewire.shipping.shipengine.index');
    }
}
