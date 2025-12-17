<?php

namespace App\Livewire\Locations;

use App\Livewire\Traits\Alert;
use App\Models\Location;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public Location $location;

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
        $this->location->delete();

        $this->dispatch('deleted');

        $this->success();
    }
}
