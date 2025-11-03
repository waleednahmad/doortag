<?php

namespace App\Livewire\Shipping\ShipEngine;

use App\Services\ShipEngineService;
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
        'length' => 12,
        'width' => 12,
        'height' => 12,
        'dimension_unit' => 'inch',
    ];

    public $carriers = [];
    public $selectedCarrier = '';
    public $carrierServices = [];
    public $selectedService = '';
    public $rates = [];
    public $selectedRate = null;
    public $trackingNumber = '';
    public $trackingResults = [];
    public $loading = false;

    protected $rules = [
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
        'package.length' => 'required|numeric|min:1',
        'package.width' => 'required|numeric|min:1',
        'package.height' => 'required|numeric|min:1',
    ];

    public function mount()
    {
        $this->loadCarriers();
        $this->setDefaultAddresses();
    }

    public function render()
    {
        return view('livewire.shipping.shipengine.index');
    }

    public function setDefaultAddresses()
    {
        // Set default ship from address
        $this->shipFromAddress = [
            'name' => 'Ahmad',
            'company_name' => 'Test Company',
            'phone' => '555-123-4567',
            'address_line1' => '1234 Main St',
            'address_line2' => '',
            'city_locality' => 'Los Angeles',
            'state_province' => 'CA',
            'postal_code' => '90210',
            'country_code' => 'US',
        ];

        // Set default ship to address
        $this->shipToAddress = [
            'name' => 'Waleed',
            'company_name' => 'Customer Company',
            'phone' => '555-987-6543',
            'address_line1' => '',
            'address_line2' => '',
            'city_locality' => '',
            'state_province' => 'NY',
            'postal_code' => '',
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
            info("================Carriers Loaded");
            info($this->carriers);
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
            info("================Carrier Services Loaded");
            info($this->carrierServices);
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

    public function getRates()
    {

        $this->validate();

        try {
            $this->loading = true;
            $this->rates = [];
            $this->selectedRate = null;

            $shipEngine = new ShipEngineService();

            $shipmentData = [
                'rate_options' => [
                    'carrier_ids' => ["se-4038210"], // For fedex only
                    'service_codes' => $this->selectedService ? [$this->selectedService] : null,
                ],
                'shipment' => [
                    'ship_to' => $shipEngine->formatAddress($this->shipToAddress),
                    'ship_from' => $shipEngine->formatAddress($this->shipFromAddress),
                    'packages' => [
                        $shipEngine->formatPackage($this->package)
                    ],
                ]
            ];

            $response = $shipEngine->getRates($shipmentData);
            $this->rates = $response['rate_response']['rates'] ?? [];
            info("================response Retrieved");
            info($response);
            info('================rates Data');
            info($this->rates);

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

            $successMessage = 'Addresses validated successfully!';

            // Update addresses with validated versions if needed
            if (!empty($fromValidation[0]['matched_address'])) {
                $validated = $fromValidation[0]['matched_address'];
                // if ($validated['address_validated']) {
                //     $successMessage .= ' Ship from address is valid.';
                // }
            }

            if (!empty($toValidation[0]['matched_address'])) {
                $validated = $toValidation[0]['matched_address'];
                // if ($validated['address_validated']) {
                //     $successMessage .= ' Ship to address is valid.';
                // }
            }

            // Check on the ToValidationStatus
            if ($toValidation[0]['status'] == 'error') {
                $this->toast()->error('Ship to address is invalid. ' . $toValidation[0]['messages'][0]['message'])->send();
                return;
            }

            // Check on the FromValidationStatus
            if ($fromValidation[0]['status'] == 'error') {
                $this->toast()->error('Ship from address is invalid. ' . $fromValidation[0]['messages'][0]['message'])->send();
                return;
            }


            $this->toast()->success($successMessage)->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to validate addresses: ' . $e->getMessage())->send();
            Log::error('Failed to validate ShipEngine addresses', ['error' => $e->getMessage()]);
        } finally {
            $this->loading = false;
        }
    }
}
