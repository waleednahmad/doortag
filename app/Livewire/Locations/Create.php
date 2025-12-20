<?php

namespace App\Livewire\Locations;

use App\Livewire\Traits\Alert;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use Alert;

    public Location $location;

    public bool $modal = false;
    public ?string $stripe_terminal_id = null;

    public function mount(): void
    {
        $this->location = new Location();
        $this->location->business_type = 'retail';
    }

    public function render(): View
    {
        return view('livewire.locations.create');
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
            'location.carrier_id' => ['required', 'string', 'max:255'],
            'location.status' => ['required', 'boolean'],
            'location.address_residential_indicator' => ['nullable', 'boolean', 'max:255'],
            'stripe_terminal_id' => ['nullable', 'string', 'max:255']
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->location->save();

        $this->dispatch('created');

        $this->location->stripePaymentMethods()->createMany([
            [
                'payment_method_name' => "Customer Card", //stripe_customer_id
                'payment_method_id' => $this->location->stripe_customer_id,
                'is_default' => false,
                'is_active' => $this->location->stripe_customer_id ? true : false,
            ],
            [
                'payment_method_name' => "Terminal Reader", // stripe_terminal_id
                'payment_method_id' =>   $this->stripe_terminal_id,
                'is_default' => false,
                'is_active' => false,
            ],
        ]);


        $this->reset();
        $this->location = new Location();

        $this->success('Location created successfully.');
    }
}
