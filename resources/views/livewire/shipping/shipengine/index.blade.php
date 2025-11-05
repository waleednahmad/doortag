<div x-data="shipEngineShippingForm()">
    <div>
        <!-- Loading Spinner -->
        @if ($loading)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4">Processing...</h3>
                    </div>
                </div>
            </div>
        @endif

        @if (!$rates)
            <form wire:submit="getRates" @submit="if(window.showGlobalLoader) window.showGlobalLoader()"
                class="space-y-6 sm:space-y-8">
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg md:text-2xl font-semibold">
                            Create ShipEngine Shipping Label
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Get rates and create labels using ShipEngine services
                        </p>
                    </x-slot:header>

                    <!-- Main Form Section -->
                    <section class="mb-[1.489em] bg-gray-50 dark:bg-gray-800 rounded-lg p-4 sm:p-6">

                        <!-- Carrier Selection -->
                        {{-- <section class="mb-6">
                            <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Carrier Options
                            </h2>
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Carrier (Optional)</label>
                                        <select wire:model.live="selectedCarrier" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="">All Carriers</option>
                                            @foreach ($carriers as $carrier)
                                                <option value="{{ $carrier['carrier_id'] }}">{{ $carrier['friendly_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($carrierServices)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Service (Optional)</label>
                                            <select wire:model="selectedService" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                <option value="">All Services</option>
                                                @foreach ($carrierServices['services'] as $service)
                                                    <option value="{{ $service['service_code'] }}">{{ $service['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section> --}}

                        <!-- Ship From Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                                Ship
                                From
                            </h2>

                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                            Sender Information</h5>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                                            Origin address details</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    @if (auth()->user()->email)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->email }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->phone)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->phone }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->address)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->address }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->address2)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Address 2
                                            </label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->address2 }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->city)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->city }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->state)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->state }}</p>
                                        </div>
                                    @endif

                                    @if (auth()->user()->zipcode)
                                        <div>
                                            <label
                                                class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zipcode
                                                *</label>
                                            <p class="text-sm sm:text-base text-gray-900 dark:text-white">
                                                {{ auth()->user()->zipcode }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>


                        <!-- Ship To Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship To (Recipient)
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
                                    <x-input label="Phone (optional)" wire:model="shipToAddress.phone" />
                                    {{-- Address --}}
                                    <x-input label="Address Line 1 *" wire:model="shipToAddress.address_line1"
                                        required />
                                    {{-- Apt / Unit / Suite / etc. --}}
                                    <x-input label="Address Line 2 (optional)"
                                        wire:model="shipToAddress.address_line2" />

                                    <div class="col-span-full md:col-span-2">
                                        {{-- City, State, Zipcode --}}
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                                            {{-- City --}}
                                            <x-input label="City *" wire:model="shipToAddress.city_locality" required />
                                            {{-- State --}}
                                            <x-input label="State *" wire:model="shipToAddress.state_province"
                                                maxlength="2" required />
                                            {{-- Zipcode --}}
                                            <x-input label="Postal Code *" wire:model="shipToAddress.postal_code"
                                                required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Ship From Section -->
                        {{-- <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship From (Sender)
                            </h2>
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    <x-input label="Name *" wire:model="shipFromAddress.name" required />
                                    <x-input label="Company (optional)" wire:model="shipFromAddress.company_name" />
                                    <x-input label="Phone (optional)" wire:model="shipFromAddress.phone" />
                                    <x-input label="Address Line 1 *" wire:model="shipFromAddress.address_line1"
                                        required />
                                    <x-input label="Address Line 2 (optional)"
                                        wire:model="shipFromAddress.address_line2" />

                                    <div class="col-span-full md:col-span-2">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                                            <x-input label="City *" wire:model="shipFromAddress.city_locality"
                                                required />
                                            <x-input label="State *" wire:model="shipFromAddress.state_province"
                                                maxlength="2" required />
                                            <x-input label="ZIP Code *" wire:model="shipFromAddress.postal_code"
                                                required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section> --}}




                        <!-- Type of Packaging -->
                        <section class="mt-3">
                            <h1
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
                                Type of Packaging</h1>

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

                                        info('selecte package is : ');
                                        info($selectedPackage);
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
                                    <i class="fa-solid fa-caret-down text-[1.3em] text-gray-900 dark:text-gray-100"
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
                                                    class="fa-solid fa-check text-[1.2em] text-blue-600 dark:text-blue-400"></i>
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
                                                <x-input type="number" wire:model="package.length" label="Length *"
                                                    step="0.1" min="1" required />
                                            </div>
                                            <div
                                                class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                <span class="text-lg sm:text-xl">×</span>
                                            </div>
                                            <div>
                                                <x-input type="number" wire:model="package.width" label="Width *"
                                                    step="0.1" min="1" required />
                                            </div>
                                            <div
                                                class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                                <span class="text-lg sm:text-xl">×</span>
                                            </div>
                                            <div>
                                                <x-input type="number" wire:model="package.height" label="Height *"
                                                    step="0.1" min="1" required />
                                            </div>
                                        </div>

                                        <!-- Tablet Layout (Medium screens) -->
                                        <div class="hidden md:grid lg:hidden md:grid-cols-3 gap-4">
                                            <div>
                                                <x-input type="number" wire:model="package.length" label="Length *"
                                                    step="0.1" min="1" required />
                                            </div>
                                            <div>
                                                <x-input type="number" wire:model="package.width" label="Width *"
                                                    step="0.1" min="1" required />
                                            </div>
                                            <div>
                                                <x-input type="number" wire:model="package.height" label="Height *"
                                                    step="0.1" min="1" required />
                                            </div>
                                        </div>

                                        <!-- Mobile Layout (Small screens) -->
                                        <div class="md:hidden space-y-3">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <x-input type="number" label="Length *"
                                                        wire:model="package.length" min="1" step="0.1"
                                                        required />
                                                </div>
                                                <div>
                                                    <x-input type="number" wire:model="package.width"
                                                        label="Width *" min="1" step="0.1" required />
                                                </div>
                                                <div>
                                                    <x-input type="number" wire:model="package.height"
                                                        label="Height *" step="0.1" min="1" required />
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
                                        <x-input label="Weight (Pounds) *" type="number" step="0.1"
                                            min="0.1" wire:model="package.weight" required />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            * Weight is calculated in pounds (LB) for shipping
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Insurance Section -->
                        <div class="my-4" x-data="{ insuranceChecked: @entangle('isInsuranceChecked') }">
                            <x-checkbox label="Insurance" wire:model.live='isInsuranceChecked'
                                hint="Enter the total value of your shipment to add coverage by InsureShield"
                                class="text-sm" />
                            <div class="mt-2">
                                <x-link href="#" color="primary" class="text-sm">
                                    View Pricing, Excluded Items, and Terms
                                </x-link>
                            </div>

                            <div x-show="insuranceChecked" x-transition class="mt-3">
                                <x-number label="Declared Package Value ($)" placeholder="Enter package value"
                                    type="number" step="0.01" wire:model='package.insured_value'
                                    min="1" />
                            </div>
                        </div>

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

        <!-- Navigation Tabs -->
        {{-- @if ($rates)
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button id="rates-tab" class="border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button active">
                        Get Rates
                    </button>
                    <button id="tracking-tab" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button">
                        Track Package
                    </button>
                </nav>
            </div>
        @endif --}}

        <!-- Rates Tab Content -->
        <div id="rates-content" class="tab-content">
            <!-- Display Quotes Results -->
            @if ($rates)
                <!-- Ship To Details Section -->
                <div class="mt-4 sm:mt-6">
                    <h1 class="text-[30px] font-[700] text-gray-900 dark:text-white leading-[1.1] mb-[12px]">
                        ShipEngine Shipping to {{ $shipToAddress['country_code'] ?? 'US' }}
                    </h1>
                    <div class="flex items-center gap-2 mb-[48px]">
                        <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                            {{ $shipToAddress['postal_code'] }} {{ $shipToAddress['country_code'] ?? 'US' }}
                        </p>
                        <i class="fa-solid fa-paste text-[1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition"
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
                    <x-slot:header>
                        <div
                            class="flex flex-col space-y-3 lg:flex-row lg:justify-between lg:items-center lg:space-y-0">
                            <div>
                                <h3 class="text-lg sm:text-xl font-semibold">ShipEngine Rate Quotes</h3>
                                @if (!empty($rates))
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Found {{ count($rates) }} shipping option(s)
                                    </p>
                                @endif
                            </div>
                        </div>
                    </x-slot:header>

                    @if (!empty($rates))
                        <div class="space-y-4">
                            @foreach ($rates as $index => $rate)
                                <div x-data="{ rateBreakdownOpen: false }"
                                    class="border rounded-lg overflow-hidden hover:shadow-md transition-all duration-300 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
                                    <!-- Main Quote Content - Clickable -->
                                    <div @click="rateBreakdownOpen = !rateBreakdownOpen"
                                        wire:click.stop="selectRate('{{ $rate['rate_id'] }}')"
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

                                        <div>
                                            {{-- $rate['original_total'] = $originalTotal;
                    $rate['margin'] = $marginMultiplier;
                    $rate['customer_margin'] = $custmoerMargin; --}}

                                            <ul>
                                                <li>
                                                    shipping amount :
                                                    ${{ $rate['shipping_amount']['amount'] ?? 'N/A' }}
                                                </li>
                                                <li>
                                                    Insurance amount :
                                                    ${{ $rate['insurance_amount']['amount'] ?? '0.00' }}
                                                </li>
                                                <li>
                                                    Confirmation Amount :
                                                    ${{ $rate['confirmation_amount']['amount'] ?? '0.00' }}
                                                </li>
                                                <li>
                                                    Requested Comparison Amount :
                                                    ${{ $rate['requested_comparison_amount']['amount'] ?? 'N/A' }}
                                                </li>
                                                <li>
                                                    Total Amount :
                                                    ${{ $rate['original_total'] ?? 'N/A' }}
                                                </li>
                                                <li>
                                                    Margin :
                                                    {{ $rate['margin'] ?? 'N/A' }}%
                                                </li>
                                                <li>
                                                    Customer Margin :
                                                    {{ $rate['customer_margin'] ?? 'N/A' }}%
                                                </li>
                                                <li>
                                                    Caluclulated Amount :
                                                    ${{ $rate['calculated_amount'] ?? 'N/A' }}
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Rate Actions -->
                                        <div class="mt-3 flex justify-between items-center">
                                            <button wire:click.stop="selectRate('{{ $rate['rate_id'] }}')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm {{ $selectedRate && $selectedRate['rate_id'] === $rate['rate_id'] ? 'bg-green-600 hover:bg-green-700' : '' }}">
                                                {{ $selectedRate && $selectedRate['rate_id'] === $rate['rate_id'] ? 'Selected' : 'Select' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($selectedRate)
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <x-button wire:click="createLabel" color="green" class="w-full sm:w-auto">
                                    Create Shipping Label
                                </x-button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400 mb-2">📦</div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                No ShipEngine quotes available
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
            }
        }

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
        });

        // Listen for label download event
        window.addEventListener('download-label', event => {
            const url = event.detail;
            const link = document.createElement('a');
            link.href = url;
            link.download = 'shipping-label.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</div>
