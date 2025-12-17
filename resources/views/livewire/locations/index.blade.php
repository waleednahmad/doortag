<div>
    <x-card>
        <x-slot:header>
            <div class=" ml-4 py-3">
                <livewire:locations.create @created="$refresh" />
            </div>
        </x-slot:header>
        <livewire:tables.locations-table />
    </x-card>

    <livewire:locations.update @updated="$refresh" />
    <livewire:locations.manage-payment-methods @updated="$refresh" />
</div>
