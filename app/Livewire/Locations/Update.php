<?php

namespace App\Livewire\Locations;

use App\Livewire\Traits\Alert;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert;

    public ?Location $location;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.locations.update');
    }

    #[On('load::location')]
    public function load(Location $location): void
    {
        $this->location = $location;

        // update the location status to boolean
        $this->location->status = (bool)$this->location->status;
        $this->location->address_residential_indicator = (bool)$this->location->address_residential_indicator;

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'location.name' => ['required', 'string', 'max:255'],
            'location.email' => ['required', 'string', 'email', 'max:255'],
            'location.phone' => ['required', 'string', 'max:255'],
            'location.company_name' => ['required', 'string', 'max:255'],
            'location.address' => ['required', 'string', 'max:255'],
            'location.address2' => ['nullable', 'string', 'max:255'],
            'location.city' => ['required', 'string', 'max:255'],
            'location.state' => ['required', 'string', 'max:255'],
            'location.zipcode' => ['required', 'string', 'max:255'],
            'location.tax_id' => ['required', 'string', 'max:255'],
            'location.years_in_business' => ['required', 'integer', 'min:0'],
            'location.business_type' => ['required', Rule::in(['retail', 'wholesale'])],
            'location.notes' => ['nullable', 'string'],
            'location.margin' => ['required', 'numeric', 'min:0'],
            'location.customer_margin' => ['required', 'numeric', 'min:0'],
            'location.tax_percentage' => ['required', 'numeric', 'min:0'],
            'location.stripe_customer_id' => ['nullable', 'string', 'max:255'],
            'location.carrier_id' => ['required', 'string', 'max:255'],
            'location.address_residential_indicator' => ['nullable', 'boolean', 'max:255'],
            'location.status' => ['required', 'boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->location->save();

        $this->dispatch('updated');

        $this->resetExcept('location');

        $this->success('Location updated successfully.');
    }
}
