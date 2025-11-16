<?php

namespace App\Livewire\Shipments;

use App\Services\ShipEngineService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions, WithPagination;

    public $labels = [];
    public $totalLabels = 0;
    public $currentPage = 1;
    public $perPage = -1; // Show all labels by default
    public $totalPages = 0;
    public $loading = false;
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $authUser = Auth::user();
        $userLabels = $authUser->shipments()->whereNotNull('label_id')->pluck('label_id')->toArray();
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();
            $response = $shipEngine->getLabels($this->currentPage, $this->perPage, $this->sortBy, $this->sortDir);

            if (isset($response['status']) && $response['status'] === 'error') {
                $errorMessage = $response['errors'][0]['message'] ?? 'Failed to load labels';
                $this->toast()->error($errorMessage)->send();
                return;
            }
            $responseLabels = $response['labels'] ?? [];
            // Filter labels to include only those associated with the authenticated user
            $filteredLabels = array_filter($responseLabels, function ($label) use ($userLabels) {
                return in_array($label['label_id'], $userLabels);
            });
            // Here iwant to add three values to each label from the shipments table: origin_total, customer_total, end_user_total
            foreach ($filteredLabels as &$label) {
                $shipment = $authUser->shipments()->where('label_id', $label['label_id'])->first();
                if ($shipment) {
                    $label['origin_total'] = $shipment->origin_total;
                    $label['customer_total'] = $shipment->customer_total;
                    $label['end_user_total'] = $shipment->end_user_total;
                    $label['signature'] = $shipment->signature_path;
                } else {
                    $label['origin_total'] = null;
                    $label['customer_total'] = null;
                    $label['end_user_total'] = null;
                }
            }


            $this->labels = array_values($filteredLabels);
            $this->totalLabels = count($this->labels);
            $this->totalPages = 1;
            // $this->totalLabels = $response['total'] ?? 0;
            // $this->totalPages = $response['pages'] ?? 0;
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load labels: ' . $e->getMessage())->send();
            $this->labels = [];
        } finally {
            $this->loading = false;
        }
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadLabels();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadLabels();
        }
    }

    public function goToPage($page)
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
        $this->loadLabels();
    }

    public function refreshLabels()
    {
        $this->loadLabels();
        $this->toast()->success('Labels refreshed successfully!')->send();
    }

    public function voidLabel($labelId)
    {
        try {
            $this->loading = true;
            $shipengine = new ShipEngineService();
            $response = $shipengine->voidLabel($labelId);

            // Handle response and provide feedback to the user
            if (isset($response['errors'])) {
                $this->toast()->error('Failed to void the label: ' . $response['errors'][0]['message'])->send();
            } elseif (isset($response['approved']) && $response['approved'] == 1) {
                $this->toast()->success('Label voided successfully.')->send();

                // Refresh the labels list
                $this->loadLabels();
            }
        } catch (\Exception $e) {
            $this->toast()->error('Failed to void label: ' . $e->getMessage())->send();
        } finally {
            $this->loading = false;
        }
    }

    public function trackingNumberCopied()
    {
        $this->toast()->success('Tracking number copied to clipboard!')->send();
    }

    public function redirectToTracking($trackingUrl)
    {
        if (!empty($trackingUrl)) {
            $this->dispatch('redirect-to-tracking', url: $trackingUrl);
        } else {
            $this->toast()->error('Tracking URL not available for this shipment.')->send();
        }
    }

    public function downloadShipmentDetails($labelId)
    {
        try {
            $authUser = Auth::user();
            $shipment = $authUser->shipments()->where('label_id', $labelId)->first();

            if (!$shipment) {
                $this->toast()->error('Shipment not found.')->send();
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
                $this->toast()->error('Invalid shipment data.')->send();
                return;
            }

            // Get tracking number from response data (shipment_data from API response)
            $trackingResponse = json_decode($shipment->shipment_data, true);
            $tracking = $trackingResponse['tracking_number'] ?? '';

            $logoPath = public_path('assets/images/logo-black.png');
            $logoBase64 = '';

            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $signatureBase64 = '';
            if ($shipment->signature_path) {
                $signaturePath = $shipment->signature_path;
                $fullSignaturePath = $signaturePath;

                // Handle different path formats - signature_path stored as "storage/signatures/2025/11/16/uuid.png"
                // Files are actually in storage/app/public/signatures/
                if (strpos($signaturePath, 'storage/') === 0) {
                    // Remove 'storage/' prefix and look in storage/app/public
                    $cleanPath = str_replace('storage/', '', $signaturePath);
                    $fullSignaturePath = storage_path('app/public/' . $cleanPath);
                } elseif (strpos($signaturePath, 'signatures/') === 0) {
                    // Path is relative to storage/app/public
                    $fullSignaturePath = storage_path('app/public/' . $signaturePath);
                } else {
                    // Assume it's relative to storage/app/public
                    $fullSignaturePath = storage_path('app/public/' . $signaturePath);
                }

                if (file_exists($fullSignaturePath)) {
                    $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($fullSignaturePath));
                } else {
                    Log::warning('Signature file not found: ' . $fullSignaturePath . ' (original: ' . $signaturePath . ')');
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
            Log::error('PDF Download Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            $this->toast()->error('Failed to generate PDF: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.shipments.index');
    }
}
