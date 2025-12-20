<?php

namespace App\Livewire\Customers;

use App\Livewire\Traits\Alert;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert;

    public ?Customer $customer;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.customers.update');
    }

    #[On('load::customer')]
    public function load(Customer $customer): void
    {
        $this->customer = $customer;
        $this->customer->is_admin = (bool)$this->customer->is_admin;
        $this->customer->can_modify_data = (bool)$this->customer->can_modify_data;

        $this->modal = true;
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
                Rule::unique('customers', 'email')->ignore($this->customer->id),
                Rule::unique('users', 'email'),
            ],
            'customer.phone' => ['required', 'string', 'max:255'],
            'customer.is_admin' => ['required', 'boolean'],
            'customer.can_modify_data' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->password !== null) {
            $this->customer->password = bcrypt($this->password);
        }
        $this->customer->save();

        $this->dispatch('updated');

        $this->resetExcept('customer');

        $this->success('Customer updated successfully.');
    }
}
