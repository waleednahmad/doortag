<?php

namespace App\Livewire\Locations;

use App\Livewire\Traits\Alert;
use App\Models\Location;
use App\Models\LocationStripePaymentMethods;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ManagePaymentMethods extends Component
{
    use Alert;

    public ?Location $location = null;
    public bool $modal = false;
    
    public $paymentMethods = [];

    public function render(): View
    {
        return view('livewire.locations.manage-payment-methods');
    }

    #[On('load::location_payment_methods')]
    public function load(Location $location): void
    {
        $this->location = $location;
        
        // Load the payment methods
        $methods = $location->stripePaymentMethods;
        
        // If no payment methods exist, create the default ones
        if ($methods->count() === 0) {
            $location->stripePaymentMethods()->createMany([
                [
                    'payment_method_name' => "Customer Card",
                    'payment_method_id' => $location->stripe_customer_id,
                    'is_default' => false,
                    'is_active' => $location->stripe_customer_id ? true : false,
                ],
                [
                    'payment_method_name' => "Terminal Reader",
                    'payment_method_id' => null,
                    'is_default' => false,
                    'is_active' => false,
                ],
            ]);
            
            // Reload the payment methods
            $methods = $location->stripePaymentMethods;
        }
        
        // Ensure we have exactly 2 methods
        if ($methods->count() >= 2) {
            $this->paymentMethods = $methods->map(function ($method) {
                return [
                    'id' => $method->id,
                    'payment_method_name' => $method->payment_method_name,
                    'payment_method_id' => $method->payment_method_id,
                    'is_default' => $method->is_default,
                    'is_active' => $method->is_active,
                ];
            })->toArray();
        }

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'paymentMethods.*.payment_method_id' => ['nullable', 'string', 'max:255'],
            'paymentMethods.*.is_default' => ['required', 'boolean'],
            'paymentMethods.*.is_active' => ['required', 'boolean'],
        ];
    }

    public function updatedPaymentMethods($value, $key)
    {
        // Check if is_default was changed
        if (str_contains($key, '.is_default')) {
            // Extract the index from the key (e.g., "0.is_default" -> 0)
            $changedIndex = (int) explode('.', $key)[0];
            
            // If this method was set to default, unset all others
            if ($this->paymentMethods[$changedIndex]['is_default']) {
                foreach ($this->paymentMethods as $index => $method) {
                    if ($index !== $changedIndex) {
                        $this->paymentMethods[$index]['is_default'] = false;
                    }
                }
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->paymentMethods as $method) {
            LocationStripePaymentMethods::where('id', $method['id'])
                ->update([
                    'payment_method_id' => $method['payment_method_id'],
                    'is_default' => $method['is_default'],
                    'is_active' => $method['is_active'],
                ]);
        }

        $this->dispatch('updated');
        
        $this->modal = false;

        $this->success('Payment methods updated successfully.');
    }
}
