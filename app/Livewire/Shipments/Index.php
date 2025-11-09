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

    #[Computed()]
    public function shipments()
    {
        $user = Auth::user();
        return $user->shipments()->latest()->paginate(10);
    }

    public function voidLabel($labelId, $shipmentId)
    {
        $shipengine = new ShipEngineService();
        $response = $shipengine->voidLabel($labelId);


        // Handle response and provide feedback to the user
        if (isset($response['errors'])) {
            $this->toast()->error('Failed to void the label: ' . $response['errors'][0]['message'])->send();
        } elseif (isset($response['approved']) && $response['approved'] == 1) {
            $this->toast()->success('Label voided successfully.')->send();

            // Update the shipment's voided_at timestamp
            $shipment = \App\Models\Shipment::find($shipmentId);
            if ($shipment) {
                $shipment->voided_at = now();
                $shipment->save();
            }
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
