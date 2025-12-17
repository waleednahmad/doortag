<?php

namespace App\Livewire\Customers;

use App\Livewire\Traits\Alert;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use Alert;

    public Customer $customer;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public bool $modal = false;

    public function mount(): void
    {
        $this->customer = new Customer();
    }

    public function render(): View
    {
        return view('livewire.customers.create');
    }

    public function rules(): array
    {
        return [
            'customer.location_id' => ['required', 'exists:locations,id'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customers', 'email'),
                Rule::unique('users', 'email'),
            ],
            'customer.phone' => ['required', 'string', 'max:255'],
            'customer.is_admin' => ['required', 'boolean'],
            'customer.can_modify_data' => ['required', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->customer->password = bcrypt($this->password);
        $this->customer->save();

        $this->dispatch('created');

        $this->reset();
        $this->customer = new Customer();

        $this->success('Customer created successfully.');
    }
}
