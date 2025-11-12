<?php

namespace App\Livewire\Shipments;

use App\Services\ShipEngineService;
use Illuminate\Support\Facades\Auth;
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

    public function render()
    {
        return view('livewire.shipments.index');
    }
}
