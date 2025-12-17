<?php

namespace App\Livewire\Customers;

use App\Livewire\Traits\Alert;
use App\Models\Customer;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public Customer $customer;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->customer->delete();

        $this->dispatch('deleted');

        $this->success();
    }
}
