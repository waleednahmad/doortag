<?php

namespace App\Livewire\Shipping\EstimateRates;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Shipment;
use App\Services\ShipEngineService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public $end_user_total, $customer_total, $origin_total;
    public $shipFromAddress = [
        'postal_code' => '',
        'country_code' => 'US',
        'address_residential_indicator' => false
    ];

    public $shipToAddress = [
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
        'insured_value' => 1,
        "insurance_provider" => "parcelguard",
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


    public function rules()
    {
        $rules = [
            'shipFromAddress.postal_code' => 'required|string|max:10',
            'shipFromAddress.country_code' => 'required|string|size:2',

            'shipToAddress.country_code' => 'required|string|size:2',
            'package.weight' => 'required|numeric|min:0.1',
        ];

        return $rules;
    }

    public function mount()
    {
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
            'postal_code' =>  '',
            'country_code' => 'US',
        ];

        // Set default ship to address
        $this->shipToAddress = [
            "postal_code" => "",
            "country_code" => "US",
            'address_residential_indicator' => false
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


    public function getRates()
    {

        $this->validate();

        try {
            $this->rates = [];

            $shipEngine = new ShipEngineService();

            $shipmentData = [
                'carrier_ids' => ["se-4121981", "se-4084605"], // For fedex only
                'from_country_code' => $this->shipFromAddress['country_code'],
                'from_postal_code' => $this->shipFromAddress['postal_code'],
                'to_country_code' => $this->shipToAddress['country_code'],
                'to_postal_code' => $this->shipToAddress['postal_code'],
                'address_residential_indicator' => $this->shipToAddress['address_residential_indicator'] == true ? 'yes' : 'no',
                'ship_date' => $this->shipDate
            ];

            if ($this->selectedPackage && $this->selectedPackage['package_code'] != 'custom') {
                $shipmentData = array_merge($shipmentData, [
                    'weight' => [
                        'value' => ceil((float) ($this->package['weight'] ?? 1)),
                        'unit' => $this->package['weight_unit'] ?? 'pound'
                    ],

                    'package_code' => $this->selectedPackage['package_code'] ?? '',
                ]);
            } else { // Custom package
                $shipmentData = array_merge($shipmentData, [
                    'weight' => [
                        'value' => ceil((float) ($this->package['weight'] ?? 1)),
                        'unit' => $this->package['weight_unit'] ?? 'pound'
                    ],
                    'dimensions' => [
                        'length' => ceil((float) ($this->package['length'] ?? 12)),
                        'width' => ceil((float) ($this->package['width'] ?? 12)),
                        'height' => ceil((float) ($this->package['height'] ?? 12)),
                        'unit' => $this->package['dimension_unit'] ?? 'inch'
                    ],
                ]);
            }

            $response = $shipEngine->getEstimatedRates($shipmentData);


            if (isset($response['rate_response']) && $response['rate_response']['status'] == 'error') {
                $this->dialog()->error('Error getting rates: ' . ($response['rate_response']['errors'][0]['message'] ?? 'Unknown error'))->send();
                return;
            }





            $responseRates = collect($response ?? []);


            // excludes the rates those have service_code as 'fedex_first_overnight'
            $responseRates = $responseRates->filter(function ($rate) {
                return $rate['service_code'] !== 'fedex_first_overnight';
            });



            $authenticatedUser = Auth::user();
            if ($authenticatedUser instanceof Customer && $authenticatedUser->location->margin > 0) {
                $formatedRates = array_map(function ($rate) use ($authenticatedUser) {
                    $shippingAmount = (float) $rate['shipping_amount']['amount'];
                    $insuranceAmount = (float) ($rate['insurance_amount']['amount'] ?? 0);
                    $confirmationAmount = (float) ($rate['confirmation_amount']['amount'] ?? 0);
                    $otherAmount = (float) ($rate['other_amount']['amount'] ?? 0);
                    $requestedComparisonAmount = (float) ($rate['requested_comparison_amount']['amount'] ?? 0);
                    $originalTotal = $shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + $requestedComparisonAmount;
                    $marginMultiplier = 1 + ($authenticatedUser->location->margin / 100);
                    $custmoerMargin = 1 + ($authenticatedUser->location->customer_margin / 100);
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
        return view('livewire.shipping.estimate-rates.index');
    }
}
