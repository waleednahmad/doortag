@props([
    'shipToAddress' => [],
    'countries' => [],
])

<section>
    <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
        Ship To
    </h2>
    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            {{-- Name --}}
            <x-input label="Name *" wire:model="shipToAddress.name" required />
            {{-- Company --}}
            <x-input label="Company (optional)" wire:model="shipToAddress.company_name" />
            {{-- Email --}}
            <x-input label="Email (optional)" wire:model="shipToAddress.email" />
            {{-- Phone --}}
            <x-input label="Phone" wire:model="shipToAddress.phone" />
            {{-- Address --}}
            <x-input label="Address Line 1 *" wire:model="shipToAddress.address_line1" required />
            {{-- Apt / Unit / Suite / etc. --}}
            <x-input label="Address Line 2 (optional)" wire:model="shipToAddress.address_line2" />

            <div class="col-span-full md:col-span-2">
                {{-- City, State, Zipcode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    {{-- City --}}
                    <x-input label="City *" wire:model="shipToAddress.city_locality" required />
                    @if ($shipToAddress['country_code'] == 'US')
                        <x-input label="State *" wire:model="shipToAddress.state_province" maxlength="2" required />
                    @else
                        <x-input label="State" wire:model="shipToAddress.state_province" />
                    @endif
                    <x-input label="Zip Code *" wire:model="shipToAddress.postal_code" required />

                    {{-- Zipcode --}}
                    {{-- Country --}}
                    <x-select.styled label="Country *" searchable wire:model.live="shipToAddress.country_code"
                        :options="$this->countries" placeholder="Select country" required />
                </div>
            </div>

            {{-- Ressidental address (checkbox) --}}
            <div class="col-span-full md:col-span-2">
                <x-checkbox label="Residential Address" wire:model.live="shipToAddress.address_residential_indicator" />
            </div>
        </div>
    </div>
</section>
