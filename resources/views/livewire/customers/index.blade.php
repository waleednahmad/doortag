<div>
    <x-card>
        <div class="mb-2 mt-4">
            <livewire:customers.create @created="$refresh" />
        </div>

        <livewire:tables.customers-table />
    </x-card>

    <livewire:customers.update @updated="$refresh" />
</div>
