@props([
    'canModifyData' => true,
    'shipFromAddress' => [],
    'countries' => [],
])

<section>
    <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
        Ship From
    </h2>
    @if ($this->userCanModifyData)
        <!-- Editable Ship From (like Ship To) -->
        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                {{-- Name --}}
                <x-input label="Name *" wire:model="shipFromAddress.name" required />
                {{-- Company --}}
                <x-input label="Company (optional)" wire:model="shipFromAddress.company_name" />
                {{-- Email --}}
                <x-input label="Email (optional)" wire:model="shipFromAddress.email" />
                {{-- Phone --}}
                <x-input label="Phone *" wire:model="shipFromAddress.phone" required />
                {{-- Address --}}
                <x-input label="Address Line 1 *" wire:model="shipFromAddress.address_line1" required />
                {{-- Apt / Unit / Suite / etc. --}}
                <x-input label="Address Line 2 (optional)" wire:model="shipFromAddress.address_line2" />

                <div class="col-span-full md:col-span-2">
                    {{-- City, State, Zipcode --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        {{-- City --}}
                        <x-input label="City *" wire:model="shipFromAddress.city_locality" required />
                        @if ($shipFromAddress['country_code'] == 'US')
                            <x-input label="State *" wire:model="shipFromAddress.state_province" maxlength="2"
                                required />
                        @else
                            <x-input label="State" wire:model="shipFromAddress.state_province" />
                        @endif
                        <x-input label="Zip Code *" wire:model="shipFromAddress.postal_code" required />

                        {{-- Country --}}
                        <x-select.styled label="Country *" searchable wire:model.live="shipFromAddress.country_code"
                            :options="$this->countries" placeholder="Select country" required />
                    </div>
                </div>

                {{-- Ressidental address (checkbox) --}}
                <div class="col-span-full md:col-span-2">
                    <x-checkbox label="Residential Address"
                        wire:model.live="shipFromAddress.address_residential_indicator" />
                </div>
            </div>
        </div>
    @else
        <!-- Read-only Ship From (current preview style) -->
        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <!-- Editable: Name -->
                <x-input label="Name *" wire:model="shipFromAddress.name" required />

                <!-- Editable: Phone -->
                <x-input label="Phone *" wire:model="shipFromAddress.phone" required />

                <x-input label="Email (optional)" wire:model="shipFromAddress.email" />

                <x-input label="Company Name " wire:model="shipFromAddress.company_name" disabled />

                <!-- Preview: Full Address -->
                <div
                    class="md:col-span-2 bg-blue-50 dark:bg-blue-900/20 rounded p-3 border border-blue-200 dark:border-blue-700">
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
                        Address
                    </p>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <p>
                            {{ $shipFromAddress['address_line1'] ?? '' }}
                            @if (!empty($shipFromAddress['address_line2']))
                                {{ $shipFromAddress['address_line2'] }}
                            @endif
                        </p>
                        <p>
                            {{ $shipFromAddress['city_locality'] ? $shipFromAddress['city_locality'] . ', ' : '' }}
                            @if (!empty($shipFromAddress['state_province']))
                                {{ $shipFromAddress['state_province'] }}
                            @endif
                            {{ $shipFromAddress['postal_code'] ?? '' }}, United States
                        </p>
                    </div>
                </div>

                <!-- Preview: Residential Indicator -->
                @if (!empty($shipFromAddress['address_residential_indicator']))
                    <div class="md:col-span-2">
                        <div
                            class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            Residential Address
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>
