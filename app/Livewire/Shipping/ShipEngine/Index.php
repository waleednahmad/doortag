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

class Index extends Component
{
    use Interactions;

    public $end_user_total, $customer_total, $origin_total;
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
        'address_residential_indicator' => false
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
        'address_residential_indicator' => true
    ];

    public $package = [
        'weight' => '',
        'weight_unit' => 'pound',
        'length' => '',
        'width' => '',
        'height' => '',
        'dimension_unit' => 'inch',
        'insured_value' => 100,
        "insurance_provider" => "parcelguard",
    ];

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

    // Sorting properties
    public $sortBy = 'price'; // 'price' or 'delivery'
    public $sortDirection = 'asc'; // 'asc' or 'desc'

    public $showModal = false;
    public $signature;

    public function updated($name, $value)
    {
        if ($name === 'isInsuranceChecked') {
            if (!$value) {
                $this->package['insured_value'] = 100;
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
            'shipToAddress.country_code' => 'required|string|size:2',

            'package.weight' => 'required|numeric|min:0.1',
            'package.insured_value' => 'required_if:isInsuranceChecked,true|numeric|min:100',
        ];

        if ($this->shipToAddress['country_code'] == 'US') {
            $rules['shipToAddress.state_province'] = 'required|string|max:2';
            $rules['shipToAddress.postal_code'] = 'required|string|max:20';
        } else {
            $rules['shipToAddress.state_province'] = 'nullable|string|max:255';
            $rules['shipToAddress.postal_code'] = 'nullable|string|max:20';
        }


        if ($this->selectedPackaging == 'custom') {
            $rules['package.length'] = 'required|numeric|min:1';
            $rules['package.width'] = 'required|numeric|min:1';
            $rules['package.height'] = 'required|numeric|min:1';
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
        return [
            'package.insured_value.min' => 'Please enter an amount of $101 or more. Amounts below this are already covered by the carrier.',
        ];
    }

    public function mount()
    {
        $this->loadCarriers();
        $this->setDefaultAddresses();
        $this->loadCarrierPackaging();
        $this->setDefaultSelectedPackage();

        // Set a default date for today date
        $this->shipDate = now()->format('m-d-Y');
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
        // Set default ship from address
        $this->shipFromAddress = [
            'name' => '',
            'company_name' => 'DoorTag',
            'phone' =>  Auth::user()->phone,
            'email' =>  '',
            'address_line1' =>  Auth::user()->address,
            'address_line2' =>  Auth::user()->address2,
            'city_locality' =>  Auth::user()->city,
            'state_province' =>  Auth::user()->state,
            'postal_code' =>  Auth::user()->zipcode,
            'country_code' => 'US',
            'address_residential_indicator' => Auth::user()->address_residential_indicator
        ];
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


            $packageData = [];

            if ($this->selectedPackage && $this->selectedPackage['package_code'] != 'custom') {
                $packageData =  [
                    'weight' => [
                        'value' => $this->package['weight'] ?? 1,
                        'unit' => $this->package['weight_unit'] ?? 'pound'
                    ],

                    'package_code' => $this->selectedPackage['package_code'] ?? '',
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
                ];
            }


            // Add insurance value if checked
            if ($this->isInsuranceChecked) {
                $packageData['insured_value'] = [
                    'amount' => $this->package['insured_value'] ?? 1,
                    'currency' => 'USD'
                ];
            }


            $shipmentData = [
                'rate_options' => [
                    'carrier_ids' => ["se-4121981"], // For fedex only
                    'service_codes' => [],
                ],
                'shipment' => [
                    'ship_date' => $this->shipDate, // Format: YYYY-MM-DD
                    'ship_to' => $shipEngine->formatAddress($this->shipToAddress),
                    'ship_from' => $shipEngine->formatAddress($this->shipFromAddress),
                    'packages' => [
                        $packageData
                    ],
                ]
            ];
            // Add insurance provider if checked
            if ($this->isInsuranceChecked) {
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

                    // Set Data to stored in the shipments table
                    $this->origin_total = number_format($originalTotal, 2);
                    $this->customer_total = number_format($originalTotal * $marginMultiplier, 2);
                    $this->end_user_total = number_format($newTotal, 2);

                    // New data 
                    $rate['original_total'] = number_format($originalTotal, 2);
                    $rate['margin'] = number_format($marginMultiplier, 2);
                    $rate['customer_margin'] = number_format($custmoerMargin, 2);
                    $rate['calculated_amount'] = number_format($newTotal, 2);
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


            $this->rates = $formatedRates;
            $this->sortRates(); // Apply default sorting
            $this->dispatch('scroll-to-top');
            $this->toast()->success('Rates retrieved successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to get rates: ' . $e->getMessage())->send();
        } finally {
            $this->loading = false;
        }
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
            $marginMultiplier = 1 + ($authenticatedUser->margin / 100);
            $custmoerMargin = 1 + ($authenticatedUser->customer_margin / 100);
            $newTotal = $originalTotal * $marginMultiplier * $custmoerMargin;
            // Set Data to stored in the shipments table
            $this->origin_total = number_format($originalTotal, 2);
            $this->customer_total = number_format($originalTotal * $marginMultiplier, 2);
            $this->end_user_total = number_format($newTotal, 2);
        } else {

            // Set Data to stored in the shipments table
            $this->origin_total = number_format($originalTotal, 2);
        }
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
        // Validate signature, it's required

        $this->validate([
            'signature' => 'required|string',
        ], [
            'signature.required' => 'Please provide a signature before creating the label.',
        ]);

        if (!$this->selectedRate) {
            $this->toast()->warning('Please select a rate first.')->send();
            return;
        }

        // Save signature as PNG file
        $signaturePath = $this->saveSignature($this->signature);

        if (!$signaturePath) {
            $this->toast()->error('Failed to save signature. Please try again.')->send();
            return;
        }

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
                Shipment::create([
                    'label_id' => $response['label_id'] ?? null,
                    'user_id' => auth('web')->check() ? auth('web')->id() : null,
                    'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
                    'shipment_data' => json_encode($response),
                    'signature_path' => "storage/" . $signaturePath,
                    'origin_total' => $this->origin_total,
                    'customer_total' => $this->customer_total ?? null,
                    'end_user_total' => $this->end_user_total ?? null,
                ]);

                $this->resetData();
                $successMessage = 'Label created successfully!';
                if (isset($response['tracking_number'])) {
                    $successMessage .= ' Tracking number: ' . $response['tracking_number'];
                }

                // Add download buttons for available formats
                $downloadButtons = '';

                // Check for PNG label
                if (isset($response['label_download']['png'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['label_download']['png'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="View PNG Label"><i class="fas fa-image mr-1"></i>PNG</button>';
                }

                // Check for PDF label
                if (isset($response['label_download']['pdf'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['label_download']['pdf'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="View PDF Label"><i class="fas fa-file-pdf mr-1"></i>PDF</button>';
                }

                // Check for Form download
                if (isset($response['form_download']['href'])) {
                    $downloadButtons .= '<button onclick="window.open(\'' . $response['form_download']['href'] . '\', \'_blank\')" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" title="View Customs Form"><i class="fas fa-file-alt mr-1"></i>Form</button>';
                }

                if (!empty($downloadButtons)) {
                    $successMessage .= '<div class="flex items-center justify-between mt-3">' . $downloadButtons . '</div>';
                }

                $this->dialog()->success($successMessage)->send();
            }
        } catch (\Exception $e) {
            $this->dialog()->error('Failed to create label: ' . $e->getMessage())->send();
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
        $this->shipToAddress = [
            'name' => '',
            'company_name' => '',
            'phone' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city_locality' => '',
            'state_province' => '',
            'postal_code' => '',
            'country_code' => 'US',
            'address_residential_indicator' => true
        ];

        $this->package = [
            'weight' => 1,
            'weight_unit' => 'pound',
            'length' => '',
            'width' => '',
            'height' => '',
            'dimension_unit' => 'inch',
            'insured_value' => 1,
            "insurance_provider" => "shipsurance",
        ];
    }


    public function backToCreateRatesPage()
    {
        $this->rates = [];
        $this->selectedRate = null;
    }


    #[Computed()]
    public function countries()
    {
        return Country::where('status', 1)->orderBy('label')->get();
    }

    public function render()
    {
        return view('livewire.shipping.shipengine.index');
    }
}
