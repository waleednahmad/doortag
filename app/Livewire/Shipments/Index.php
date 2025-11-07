<?php

namespace App\Livewire\Shipments;

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
