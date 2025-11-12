<div x-data="shipEngineShippingForm()">
    <div>

        <!-- Loading Spinner -->
        <div class="fixed inset-0 bg-gray-600/90 bg-opacity-50 overflow-y-auto h-full w-full z-50
        flex items-center justify-center
        "
            wire:loading>
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Loading...</h3>
                </div>
            </div>
        </div>


        @if (!$rates)
            <form wire:submit="getRates" @submit="if(window.showGlobalLoader) window.showGlobalLoader()"
                class="space-y-6 sm:space-y-8">
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg md:text-2xl font-semibold">
                            Check Rates
                        </h3>
                    </x-slot:header>

                    <!-- Main Form Section -->
                    <section class="mb-[1.489em] bg-gray-50 dark:bg-gray-800 rounded-lg p-4 sm:p-6">

                        <!-- Ship From Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship From
                            </h2>
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">

                                    <div class="col-span-full md:col-span-2">
                                        {{-- City, State, Zipcode --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">

                                            @if ($shipFromAddress['country_code'] == 'US')
                                                <x-input label="Postal Code *" wire:model="shipFromAddress.postal_code"
                                                    required />
                                            @else
                                                <x-input label="Postal Code" wire:model="shipFromAddress.postal_code" />
                                            @endif
                                            {{-- Country --}}
                                            <x-select.styled label="Country *" searchable disabled
                                                wire:model.live="shipFromAddress.country_code" :options="$this->countries"
                                                placeholder="Select country" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <!-- Ship To Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship To
                            </h2>
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    <div class="col-span-full md:col-span-2">
                                        {{-- City, State, Zipcode --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                            {{-- City --}}
                                            @if ($shipToAddress['country_code'] == 'US')
                                                <x-input label="Postal Code *" wire:model="shipToAddress.postal_code"
                                                    required />
                                            @else
                                                <x-input label="Postal Code" wire:model="shipToAddress.postal_code" />
                                            @endif
                                            {{-- Zipcode --}}
                                            {{-- Country --}}
                                            <x-select.styled label="Country *" searchable
                                                wire:model.live="shipToAddress.country_code" :options="$this->countries"
                                                placeholder="Select country" required />
                                        </div>
                                    </div>

                                    {{-- Ressidental address (checkbox) --}}
                                    <div class="col-span-full md:col-span-2">
                                        <x-checkbox label="Residential Address"
                                            wire:model.live="shipToAddress.address_residential_indicator" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Type of Packaging -->
                        <section class="mt-3">
                            <h1
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                                Type of Packaging
                            </h1>

                            <div x-data="{ packagingOpen: false }" class="rounded-[5px] border-2 transition-colors duration-200"
                                :class="packagingOpen ? 'border-[#00a9ff]' :
                                    'border-gray-300 dark:border-gray-600 bg-gradient-to-b from-white to-gray-100 dark:from-gray-700 dark:to-gray-800'">

                                <!-- Toggle Header - Shows Selected Package -->
                                <div @click="packagingOpen = !packagingOpen"
                                    class="w-full flex items-center justify-between p-[10px] cursor-pointer rounded-[4px] transition-colors"
                                    :class="packagingOpen ? 'hover:bg-blue-50 dark:hover:bg-blue-900/20' :
                                        'hover:bg-gray-50 dark:hover:bg-gray-600'">
                                    @php
                                        $selectedPackage = collect($carrierPackaging)->firstWhere(
                                            'package_code',
                                            $selectedPackaging,
                                        );
                                    @endphp
                                    <div class="flex items-center">
                                        {{-- img container --}}
                                        <span class="w-[130px] h-[90px]  flex items-center justify-center">
                                            @if ($selectedPackage['package_code'] === 'custom')
                                                <img src="{{ asset('assets/images/Parcel-box.png') }}" alt="Parcel"
                                                    class=" w-full object-contain" />
                                            @else
                                                <img src="{{ asset('assets/images/fedex.svg') }}" alt="Parcel"
                                                    class="w-full object-contain" />
                                            @endif
                                        </span>
                                        <div class="ml-[.9em]">

                                            <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100">
                                                {{ $selectedPackage['name'] ?? 'Custom Box or Rigid Packaging' }}
                                            </h1>
                                            <p
                                                class="text-[.824em] font-[400] text-gray-500 dark:text-gray-400 mt-[3px]">
                                                {{ $selectedPackage['description'] ?? 'Any custom box or thick parcel' }}
                                            </p>
                                        </div>
                                    </div>
                                    <i class="fas fa-caret-down text-[1.3em] text-gray-900 dark:text-gray-100"
                                        :class="packagingOpen ? 'rotate-180' : ''"
                                        style="transition: transform 0.2s;"></i>
                                </div>

                                <!-- Package Options -->
                                <div x-show="packagingOpen" x-transition @click.away="packagingOpen = false">
                                    @forelse ($carrierPackaging as $index => $package)
                                        <div wire:click="selectPackaging('{{ $package['package_code'] }}')"
                                            @click="packagingOpen = false"
                                            class="w-full flex items-center justify-between p-[10px] cursor-pointer transition-colors {{ $selectedPackaging === $package['package_code'] ? 'bg-blue-50 dark:bg-blue-900/20 border-[#00a9ff]' : 'hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-[#00a9ff] dark:hover:border-blue-400' }} {{ $index === 0 ? 'border-t-[1px] border-gray-300 dark:border-gray-600' : '' }}">
                                            <div class="flex items-center">
                                                @if ($package['package_code'] === 'custom')
                                                    <img src="{{ asset('assets/images/Parcel-box.png') }}"
                                                        alt="Custom Package" class="object-contain w-[60px] h-[60px]" />
                                                @else
                                                    <img src="{{ asset('assets/images/fedex.svg') }}"
                                                        alt="FedEx Package" class="object-contain w-[60px] h-[60px]" />
                                                @endif
                                                <div class="ml-[.9em]">
                                                    <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100">
                                                        {{ $package['name'] }}
                                                    </h1>
                                                    <p
                                                        class="text-[.824em] font-[400] text-gray-500 dark:text-gray-400 mt-[3px]">
                                                        {{ $package['description'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            @if ($selectedPackaging === $package['package_code'])
                                                <i
                                                    class="fas fa-check text-[1.2em] text-blue-600 dark:text-blue-400"></i>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            <p>No packaging options available</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </section>





                        <!-- Package Details Section -->
                        <section class="mt-3">
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    Package Details
                                </h2>
                            </div>

                            <div
                                class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        Package 1
                                    </h5>
                                </div>

                                <!-- Package Dimensions -->
                                @if ($selectedPackage['package_code'] == 'custom')
                                    <div class="mb-6 sm:mb-8"
                                        x-hide="{{ $selectedPackage['package_code'] !== 'custom' }}">
                                        <h6
                                            class="text-sm sm:text-base font-medium text-gray-800 dark:text-gray-200 mb-3 sm:mb-4">
                                            Package Dimensions (Inches)
                                        </h6>

                                        <!-- Desktop Layout (Large screens) -->
                                        <div class="hidden lg:grid lg:grid-cols-5 gap-4 items-end">
                                            <div>
                                                <x-number wire:model="package.length" label="Length *" step="0.1"
                                                    min="1" required />
                                            </div>
                                            <div
                                                class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                <span class="text-lg sm:text-xl">×</span>
                                            </div>
                                            <div>
                                                <x-number wire:model="package.width" label="Width *" step="0.1"
                                                    min="1" required />
                                            </div>
                                            <div
                                                class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                <span class="text-lg sm:text-xl">×</span>
                                            </div>
                                            <div>
                                                <x-number wire:model="package.height" label="Height *" step="0.1"
                                                    min="1" required />
                                            </div>
                                        </div>

                                        <!-- Tablet Layout (Medium screens) -->
                                        <div class="hidden md:grid lg:hidden md:grid-cols-3 gap-4">
                                            <div>
                                                <x-number wire:model="package.length" label="Length *" step="0.1"
                                                    min="1" required />
                                            </div>
                                            <div>
                                                <x-number wire:model="package.width" label="Width *" step="0.1"
                                                    min="1" required />
                                            </div>
                                            <div>
                                                <x-number wire:model="package.height" label="Height *" step="0.1"
                                                    min="1" required />
                                            </div>
                                        </div>

                                        <!-- Mobile Layout (Small screens) -->
                                        <div class="md:hidden space-y-3">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <x-number label="Length *" wire:model="package.length"
                                                        min="1" step="0.1" required />
                                                </div>
                                                <div>
                                                    <x-number wire:model="package.width" label="Width *"
                                                        min="1" step="0.1" required />
                                                </div>
                                                <div>
                                                    <x-number wire:model="package.height" label="Height *"
                                                        step="0.1" min="1" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Weight -->
                                <div class="mb-6 sm:mb-8">
                                    <h6
                                        class="text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">
                                        Package Weight
                                    </h6>
                                    <div class="max-w-md">
                                        <x-number label="Weight (Pounds) *" step="0.1" min="0.1"
                                            wire:model="package.weight" required />
                                    </div>
                                </div>
                            </div>
                        </section>


                        <section class="mt-3">
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    <x-date label="Shipment Date" :min-date="now()" :max-date="now()->addWeek()"
                                        format="MM-DD-YYYY" wire:model="shipDate" required />
                                </div>
                            </div>
                        </section>
                    </section>


                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        {{-- <x-button wire:click="validateAddresses" color="gray" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="validateAddresses">Validate Addresses</span>
                            <span wire:loading wire:target="validateAddresses">Validating...</span>
                        </x-button> --}}

                        <x-button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 sm:px-8 sm:py-3 w-full sm:w-auto">
                            <span wire:loading.remove wire:target="getRates">Get Rates</span>
                            <span wire:loading wire:target="getRates">Getting Rates...</span>
                        </x-button>
                    </div>
                </x-card>
            </form>
        @endif

        <!-- Rates Tab Content -->
        <div id="rates-content" class="tab-content">
            <!-- Display Quotes Results -->
            @if ($rates)
                <!-- Ship To Details Section -->
                <div class="mt-4 sm:mt-6">
                    <h1 class="text-[30px] font-[700] text-gray-900 dark:text-white leading-[1.1] mb-[12px]">
                        Shipping to {{ $shipToAddress['country_code'] ?? 'US' }}
                    </h1>
                    <div class="flex items-center gap-2 mb-[48px]">
                        <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                            {{ $shipToAddress['postal_code'] }} {{ $shipToAddress['country_code'] ?? 'US' }}
                        </p>
                        <i class="fas fa-paste text-[1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition"
                            onclick="navigator.clipboard.writeText('{{ $shipToAddress['postal_code'] }} {{ $shipToAddress['country_code'] ?? 'US' }}')"
                            title="Copy to clipboard"></i>
                    </div>
                </div>

                <!-- Shipment Details Section -->
                <div class="mb-[48px] py-[7px]" x-data="{ shipmentDetailsOpen: false }">
                    <label @click="shipmentDetailsOpen = !shipmentDetailsOpen"
                        class="flex cursor-pointer space-x-[5px]">
                        <div
                            class="w-[20px] h-[20px] border-[2px] border-gray-500 dark:border-gray-400 rounded-[50%] flex items-center justify-center cursor-pointer">
                            <span x-show="!shipmentDetailsOpen"
                                class="text-[12px] text-gray-500 dark:text-gray-400">+</span>
                            <span x-show="shipmentDetailsOpen"
                                class="text-[12px] text-gray-500 dark:text-gray-400">-</span>
                        </div>
                        <p class="font-[500] text-[15px] text-gray-700 dark:text-gray-300">Shipment Details</p>
                    </label>
                    <div x-show="shipmentDetailsOpen" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="p-[16px_16px_16px_24px] flex lg:flex-row flex-col justify-between bg-gray-100 dark:bg-gray-800 border-[2px] border-gray-300 dark:border-gray-600 rounded-[5px] mt-[10px] lg:gap-0 gap-[16px]">

                        <!-- Ship From Address -->
                        <div class="lg:w-[33.3333%] w-full text-[14px]">
                            <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Ship
                                From Address</h1>
                            <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">
                                {{ $shipFromAddress['postal_code'] }}</p>
                            <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">
                                {{ $shipFromAddress['country_code'] ?? 'US' }}</p>
                        </div>

                        <!-- Package Details -->
                        <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[8px] pl-0">
                            <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">
                                Package Details</h1>
                            <div class="mb-2">
                                <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                    Package 1: <span
                                        class="pl-[4px] font-[400]">{{ $selectedCarrier ? collect($carriers)->firstWhere('carrier_id', $selectedCarrier)['friendly_name'] ?? 'Multiple Services' : 'Multiple Services' }}</span>
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                    Weight: <span class="pl-[4px] font-[400]">{{ $package['weight'] }}
                                        {{ $package['weight_unit'] }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[16px] pl-0">
                            <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">
                                Service Details</h1>
                            <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                Carrier: <span
                                    class="pl-[4px] font-[400]">{{ $selectedCarrier ? collect($carriers)->firstWhere('carrier_id', $selectedCarrier)['friendly_name'] ?? 'All Carriers' : 'All Carriers' }}</span>
                            </p>
                            <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                Currency: <span class="pl-[4px] font-[400]">USD</span>
                            </p>
                        </div>
                    </div>
                </div>

                <x-card class="mt-4 sm:mt-6">
                    {{-- <x-slot:header> --}}
                    <div class="space-y-4 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 sm:pb-2">
                        <!-- Title and Count -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                            <div>
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                                    Rate Quotes
                                </h3>
                                @if (!empty($rates))
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Found {{ count($rates) }} shipping option(s)
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if (!empty($rates))
                            <!-- Enhanced Sorting Section -->
                            <div
                                class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <!-- Sort Label -->
                                    <div class="flex items-center">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-sort mr-2 text-gray-500 dark:text-gray-400"></i>
                                            <span class="font-medium">Sort by:</span>
                                        </div>
                                    </div>

                                    <!-- Sort Buttons -->
                                    <div
                                        class="flex items-center justify-between gap-1 bg-white dark:bg-gray-700 rounded-lg p-1 shadow-sm border border-gray-200 dark:border-gray-600">
                                        <button wire:click="sortByPrice"
                                            class="group relative inline-flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-all duration-200 ease-in-out
                                                {{ $sortBy === 'price'
                                                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/25'
                                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-5 h-5 mr-2">
                                                    <i class="fas fa-dollar-sign text-sm"></i>
                                                </div>
                                                <span>Price</span>
                                                <!-- Always show both arrows, highlight the active one -->
                                                <div class="ml-3 flex flex-col items-center justify-center">
                                                    <i
                                                        class="fas fa-caret-up text-xs transition-all duration-200
                                                            {{ $sortBy === 'price' && $sortDirection === 'asc'
                                                                ? 'opacity-100 text-white'
                                                                : ($sortBy === 'price'
                                                                    ? 'opacity-50 text-white'
                                                                    : 'opacity-40 text-gray-400') }}"></i>
                                                    <i
                                                        class="fas fa-caret-down text-xs transition-all duration-200 -mt-1
                                                            {{ $sortBy === 'price' && $sortDirection === 'desc'
                                                                ? 'opacity-100 text-white'
                                                                : ($sortBy === 'price'
                                                                    ? 'opacity-50 text-white'
                                                                    : 'opacity-40 text-gray-400') }}"></i>
                                                </div>
                                            </div>
                                            @if ($sortBy !== 'price')
                                                <div
                                                    class="absolute inset-0 rounded-md bg-gradient-to-r from-blue-500/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                </div>
                                            @endif
                                        </button>

                                        <button wire:click="sortByDelivery"
                                            class="group relative inline-flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-all duration-200 ease-in-out
                                                {{ $sortBy === 'delivery'
                                                    ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/25'
                                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-5 h-5 mr-2">
                                                    <i class="fas fa-clock text-sm"></i>
                                                </div>
                                                <span>Delivery</span>
                                                <!-- Always show both arrows, highlight the active one -->
                                                <div class="ml-3 flex flex-col items-center justify-center">
                                                    <i
                                                        class="fas fa-caret-up text-xs transition-all duration-200
                                                            {{ $sortBy === 'delivery' && $sortDirection === 'asc'
                                                                ? 'opacity-100 text-white'
                                                                : ($sortBy === 'delivery'
                                                                    ? 'opacity-50 text-white'
                                                                    : 'opacity-40 text-gray-400') }}"></i>
                                                    <i
                                                        class="fas fa-caret-down text-xs transition-all duration-200 -mt-1
                                                            {{ $sortBy === 'delivery' && $sortDirection === 'desc'
                                                                ? 'opacity-100 text-white'
                                                                : ($sortBy === 'delivery'
                                                                    ? 'opacity-50 text-white'
                                                                    : 'opacity-40 text-gray-400') }}"></i>
                                                </div>
                                            </div>
                                            @if ($sortBy !== 'delivery')
                                                <div
                                                    class="absolute inset-0 rounded-md bg-gradient-to-r from-emerald-500/10 to-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                <!-- Enhanced Sort Status -->
                                <div class="mt-3 flex items-center justify-center sm:justify-start">
                                    <div
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                            {{ $sortBy === 'price'
                                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700'
                                                : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700' }}">
                                        <div class="flex items-center">
                                            <i
                                                class="fas fa-{{ $sortBy === 'price' ? 'dollar-sign' : 'clock' }} mr-2 text-xs"></i>
                                            <span>
                                                {{ $sortBy === 'price' ? 'Price' : 'Delivery Time' }} -
                                                {{ $sortDirection === 'asc' ? ($sortBy === 'price' ? 'Low to High' : 'Earliest First') : ($sortBy === 'price' ? 'High to Low' : 'Latest First') }}
                                            </span>
                                            <div class="ml-2 flex items-center">
                                                <i
                                                    class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs font-bold"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- </x-slot:header> --}}

                    @if (!empty($rates))
                        <div class="space-y-4">
                            @foreach ($rates as $index => $rate)
                                <div x-data="{ rateBreakdownOpen: false }"
                                    class="border rounded-lg overflow-hidden hover:shadow-md transition-all duration-300 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
                                    <!-- Main Quote Content - Clickable -->
                                    <div @click="rateBreakdownOpen = !rateBreakdownOpen"
                                        class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                        <div
                                            class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-3 sm:space-y-0">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <!-- Carrier Logo or Icon -->
                                                <div
                                                    class="flex-shrink-0 mt-1 w-[55px] h-[55px] flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded p-2">
                                                    <img src="{{ asset('assets/images/fedex.svg') }}"
                                                        class=" object-contain w-full h-full" alt="FedEx" />
                                                </div>

                                                <div class="flex-1">
                                                    <h4
                                                        class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">
                                                        {{-- {{ $rate['carrier_friendly_name'] ?? 'Unknown Carrier' }} - --}}
                                                        {{ $rate['service_type'] ?? 'Unknown Service' }}
                                                    </h4>
                                                    {{-- <p
                                                        class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                        Service Code: {{ $rate['service_code'] ?? 'N/A' }}
                                                    </p> --}}
                                                    @if (isset($rate['carrier_delivery_days']))
                                                        <p
                                                            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 ">
                                                            Estimated Delivery:
                                                            <span class="font-bold">
                                                                {{ $rate['carrier_delivery_days'] }}
                                                            </span>
                                                            @if ($rate['delivery_days'])
                                                                ({{ $rate['delivery_days'] }}
                                                                {{ Str::plural('day', $rate['delivery_days']) }})
                                                            @endif
                                                        </p>
                                                    @endif
                                                    {{-- @if (isset($rate['zone']))
                                                        <p class="text-xs sm:text-sm text-blue-600 dark:text-blue-400">
                                                            Zone: {{ $rate['zone'] }}
                                                        </p>
                                                    @endif --}}
                                                </div>
                                            </div>

                                            <!-- Rate Details -->
                                            <div class="text-center sm:text-right space-y-2">
                                                <div
                                                    class="border rounded p-2 bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700">
                                                    <div
                                                        class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1">
                                                        Shipping Rate
                                                    </div>
                                                    <div
                                                        class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                                        ${{ $rate['calculated_amount'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (isset($rate['warning_messages']) && count($rate['warning_messages']) > 0)
                                            <div
                                                class="mt-3 p-2 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded">
                                                <div class="text-xs text-orange-600 dark:text-orange-400">
                                                    <strong>Warning:</strong>
                                                    {{ implode(', ', $rate['warning_messages']) }}
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($rate['error_messages']) && count($rate['error_messages']) > 0)
                                            <div
                                                class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded">
                                                <div class="text-xs text-red-600 dark:text-red-400">
                                                    <strong>Warning:</strong>
                                                    {{ implode(', ', $rate['error_messages']) }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                            <x-button href="{{ route('shipping.shipengine.index') }}" color="green"
                                class="w-full sm:w-auto" loading="createLabel">
                                Ship Now
                            </x-button>
                            <x-button wire:click="backToCreateRatesPage" color="blue" class="w-full sm:w-auto"
                                loading="backToCreateRatesPage">
                                Back
                            </x-button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400 mb-2">📦</div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                No quotes available
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Please check your shipping details and try again.
                            </p>
                        </div>
                    @endif
                </x-card>
            @endif
        </div>

        <!-- Tracking Tab Content -->
        <div id="tracking-content" class="tab-content hidden">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg md:text-2xl font-semibold">
                        Track Package
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Enter tracking number to get shipment status
                    </p>
                </x-slot:header>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 sm:p-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <x-input label="Tracking Number" wire:model="trackingNumber"
                                placeholder="Enter tracking number" />
                        </div>

                        <div class="flex items-end">
                            <x-button wire:click="trackPackage" wire:loading.attr="disabled">
                                <span wire:loading.remove>Track Package</span>
                                <span wire:loading>Tracking...</span>
                            </x-button>
                        </div>
                    </div>
                </div>

                <!-- Tracking Results -->
                @if ($trackingResults)
                    <div
                        class="mt-6 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tracking Information</h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</p>
                                    <p class="text-lg text-gray-900 dark:text-white">
                                        {{ $trackingResults['status_description'] ?? 'Unknown' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</p>
                                    <p class="text-lg text-gray-900 dark:text-white">
                                        {{ $trackingResults['carrier_code'] ?? 'Unknown' }}</p>
                                </div>
                            </div>

                            @if (isset($trackingResults['events']) && count($trackingResults['events']) > 0)
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Tracking Events</h4>
                                    <div class="space-y-2">
                                        @foreach ($trackingResults['events'] as $event)
                                            <div
                                                class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-r">
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ $event['description'] ?? 'Unknown Event' }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $event['occurred_at'] ?? 'Unknown Date' }}
                                                    @if ($event['city_locality'] || $event['state_province'])
                                                        - {{ $event['city_locality'] ?? '' }}
                                                        {{ $event['state_province'] ?? '' }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <script>
        function shipEngineShippingForm() {
            return {
                // Any additional data needed for the form
                init() {
                    // Prevent mouse wheel scroll from changing number input values on macOS
                    this.$nextTick(() => {
                        document.querySelectorAll('input[type="number"]').forEach(input => {
                            input.addEventListener('wheel', (e) => e.preventDefault(), {
                                passive: false
                            });
                        });
                    });
                }
            }
        }


        document.addEventListener('livewire:init', () => {

            // Set selected values when school is loaded
            Livewire.on('scroll-to-top', () => {
                window.scrollTo(0, 0);
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });

                    // Add active class to clicked tab
                    this.classList.add('border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500');

                    // Hide all content
                    contents.forEach(content => content.classList.add('hidden'));

                    // Show corresponding content
                    const targetId = this.id.replace('-tab', '-content');
                    document.getElementById(targetId).classList.remove('hidden');
                });
            });

            // Set initial active tab only if rates exist
            if (document.getElementById('rates-tab')) {
                document.getElementById('rates-tab').classList.add('border-blue-500', 'text-blue-600');
                document.getElementById('rates-tab').classList.remove('border-transparent', 'text-gray-500');
            }

            // Setup wheel prevention and hide increment/decrement buttons for all number inputs
            const preventNumberScroll = (e) => e.preventDefault();

            const setupNumberInputs = () => {
                document.querySelectorAll('input[type="number"]').forEach(input => {
                    // Check if we've already setup this input
                    if (input.dataset.wheelSetup === 'true') return;

                    // Prevent wheel scroll
                    input.addEventListener('wheel', preventNumberScroll, {
                        passive: false
                    });

                    // Hide the increment/decrement buttons (siblings)
                    const parent = input.parentElement;
                    if (parent) {
                        const buttons = parent.querySelectorAll('button');
                        buttons.forEach(button => {
                            button.style.display = 'none';
                        });
                    }

                    // Mark this input as setup
                    input.dataset.wheelSetup = 'true';
                });
            };

            // Initial setup
            setupNumberInputs();

            // Use MutationObserver to watch for changes in the DOM
            const observer = new MutationObserver((mutations) => {
                // Check if any mutation involves new number inputs
                let hasNumberInputs = false;
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length > 0) {
                        hasNumberInputs = true;
                    }
                });

                if (hasNumberInputs) {
                    setupNumberInputs();
                }
            });

            // Start observing the document for changes
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: false,
                characterData: false
            });

            // Also listen for Livewire events
            window.addEventListener('livewire:updated', setupNumberInputs);
        });

        // Listen for label download event
        window.addEventListener('download-label', event => {
            const url = event.detail;
            // Open PDF in new tab instead of downloading
            window.open(url, '_blank');
        });
    </script>
</div>
