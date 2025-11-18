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
                            Create Shipping Label
                        </h3>
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

                        <!-- Ship From Section - Mixed View -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                                Ship From
                            </h2>
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
                                        <p
                                            class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
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

                        </section>


                        <!-- Ship To Section -->
                        <section>
                            <h2
                                class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
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
                                    <x-input label="Address Line 1 *" wire:model="shipToAddress.address_line1"
                                        required />
                                    {{-- Apt / Unit / Suite / etc. --}}
                                    <x-input label="Address Line 2 (optional)"
                                        wire:model="shipToAddress.address_line2" />

                                    <div class="col-span-full md:col-span-2">
                                        {{-- City, State, Zipcode --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                            {{-- City --}}
                                            <x-input label="City *" wire:model="shipToAddress.city_locality" required />
                                            <x-input label="Postal Code *" wire:model="shipToAddress.postal_code"
                                                required />

                                            @if ($shipToAddress['country_code'] == 'US')
                                                <x-input label="State *" wire:model="shipToAddress.state_province"
                                                    maxlength="2" required />
                                            @else
                                                <x-input label="State" wire:model="shipToAddress.state_province" />
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

                                <div @click="packagingOpen = !packagingOpen"
                                    class="w-full flex items-center justify-between p-[10px] cursor-pointer rounded-[4px] transition-colors"
                                    :class="packagingOpen ? 'hover:bg-blue-50 dark:hover:bg-blue-900/20' :
                                        'hover:bg-gray-50 dark:hover:bg-gray-600'">
                                    <div class="flex items-center">
                                        {{-- img container --}}
                                        <span class="w-[130px] h-[90px]  flex items-center justify-center">
                                            @if ($selectedPackage && $selectedPackage['package_code'] === 'custom')
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

                        <!-- Insurance Section -->
                        <div class="my-4 flex flex-col  gap-3" x-data="{ insuranceChecked: @entangle('isInsuranceChecked') }">
                            <x-checkbox label="Insurance" wire:model.live='isInsuranceChecked'
                                hint="Enter the total value of your shipment to add coverage by InsureShield"
                                class="text-sm" />

                            <div x-show="insuranceChecked" x-transition>
                                <x-number label="Declared Package Value ($) *" placeholder="Enter package value"
                                    :required="$this->isInsuranceChecked" step="0.01" wire:model='package.insured_value'
                                    min="100" />
                            </div>
                        </div>


                        <section class="mt-3">
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                    <x-date label="Shipment Date" :min-date="now()" :max-date="now()->addWeek()"
                                        format="YYYY-MM-DD" wire:model.live="shipDate" required
                                        storage-format="YYYY-MM-DD" />
                                </div>
                            </div>
                        </section>

                        {{-- For the international shipments --}}
                        @if ($shipToAddress['country_code'] != 'US')
                            <!-- Customs Information Section -->
                            <section class="mt-4">
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
                                        Customs Information
                                    </h2>
                                    <div class="flex gap-2">
                                        <x-button type="button" sm color="purple" loading="addCustomsItem"
                                            wire:click="addCustomsItem">
                                            <x-slot:left>
                                                <i class="fas fa-plus mr-1"></i>
                                            </x-slot:left>
                                            Add Item
                                        </x-button>
                                    </div>
                                </div>

                                <div
                                    class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">

                                    <!-- Customs General Info -->
                                    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {{-- signer name --}}
                                        <div class="col-span-2">
                                            <x-input label="Sign Customs Form As *" type="text"
                                                wire:model="customs.signer" placeholder="Enter signer name"
                                                required />
                                        </div>

                                        <x-select.styled label="Contents Type *" wire:model.live="customs.contents"
                                            :options="[
                                                ['label' => 'Merchandise', 'value' => 'merchandise'],
                                                ['label' => 'Documents', 'value' => 'documents'],
                                                // ['label' => 'Gift', 'value' => 'gift'],
                                                // ['label' => 'Sample', 'value' => 'sample'],
                                            ]" placeholder="Select contents type" required />

                                        <x-select.styled label="Non-Delivery Action *"
                                            wire:model="customs.non_delivery" :options="[
                                                ['label' => 'Return to Sender', 'value' => 'return_to_sender'],
                                                ['label' => 'Treat as Abandoned', 'value' => 'treat_as_abandoned'],
                                            ]"
                                            placeholder="Select non-delivery action" required />
                                    </div>

                                    <!-- Customs Items -->
                                    @foreach ($customs['customs_items'] as $customItemIndex => $customItem)
                                        <div wire:key="customs-item-{{ $customItemIndex }}"
                                            class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 sm:p-4 mb-4 border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center justify-between mb-4">
                                                <h5
                                                    class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                                    Item {{ $customItemIndex + 1 }}
                                                </h5>
                                                @if (count($customs['customs_items']) > 1)
                                                    <x-button type="button" sm color="red" light
                                                        loading="removeCustomsItem({{ $customItemIndex }})"
                                                        wire:click="removeCustomsItem({{ $customItemIndex }})">
                                                        <x-slot:left>
                                                            <i class="fas fa-trash mr-1"></i>
                                                        </x-slot:left>
                                                    </x-button>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <x-input label="Descripe what you're shipping *" type="text"
                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.description"
                                                    placeholder="e.g., Cotton T-Shirt" required />

                                                <x-number label="Quantity *"
                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.quantity"
                                                    min="1" required />

                                                <x-number label="Total Value in $ *" step="0.01"
                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.value.amount"
                                                    min="0.01" required />

                                                <x-number label="Item(s) Total Weight (lbs) *" step="0.01"
                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.weight.value"
                                                    min="0.01" required />

                                                @if ($customs['contents'] != 'documents')
                                                    <div class="col-span-2 sm:col-span-1">
                                                        <div class="grid grid-cols-6 gap-2">
                                                            <div class="col-span-5">
                                                                <x-input label="Harmonized Tariff Code *"
                                                                    type="text" required
                                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.harmonized_tariff_code"
                                                                    placeholder="e.g., 6109.10.00" />
                                                            </div>
                                                            <div class="col-span-1 flex items-end">
                                                                <x-button text="Search" color="purple"
                                                                    href="https://uscensus.prod.3ceonline.com/ui/"
                                                                    target='_blank' size="sm" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <x-select.styled label="Country of Origin *" searchable
                                                    wire:model="customs.customs_items.{{ $customItemIndex }}.country_of_origin"
                                                    :options="$this->countries" placeholder="Select country" required />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- International Tax IDs -->
                                <section
                                    class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-medium text-gray-900">International Tax IDs</h3>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <x-input label=" Sender Tax ID" wire:model="tax_identifiers.0.value"
                                            placeholder="Enter tax ID number" />
                                        <x-input label=" Recipient Tax ID" wire:model="tax_identifiers.1.value"
                                            placeholder="Enter tax ID number" />
                                    </div>
                                </section>
                            </section>
                        @endif
                    </section>


                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        @if (isset($errors) && $errors->any())
                            <div class="w-full">
                                <x-alert type="error" title="Please fix the errors below:">
                                    <ul class="mt-2 list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </x-alert>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-end justify-end">
                        <x-button type="submit" wire:loading.attr="disabled" color="green"
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
                        Shipping to {{ $shipToAddressCountryFullName ?? 'US' }}
                    </h1>
                    <div class="flex items-center gap-2 mb-[48px]">
                        <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                            {{ $shipToAddress['postal_code'] }} {{ $shipToAddressCountryFullName ?? 'US' }}
                        </p>
                        <i class="fas fa-paste text-[1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition"
                            onclick="navigator.clipboard.writeText('{{ $shipToAddress['postal_code'] }} {{ $shipToAddressCountryFullName ?? 'US' }}')"
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
                                        @if (isset($rate['error_messages']) && count($rate['error_messages']) > 0)
                                            <div
                                                class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded">
                                                <div class="text-xs text-red-600 dark:text-red-400">
                                                    <strong>Warning:</strong>
                                                    {{ implode(', ', $rate['error_messages']) }}
                                                </div>
                                            </div>
                                        @endif

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
                        {{-- Section for the $packagingAmount --}}
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                <x-number wire:model="packagingAmount" label="Packaging Amount" step="0.1"
                                    min="0" required />
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between">


                            <x-modal scrollable wire="showModal" size="4xl"
                                x-on:open="$wire.set('signature', null); setTimeout(() => { window.dispatchEvent(new Event('resize')) }, 300), $focusOn('modal-signature')">
                                <x-slot:title>
                                    Shipment Details Review
                                </x-slot:title>
                                <div id="modal-content" class="space-y-6">
                                    <!-- Ship From Section -->
                                    <div class="border-b pb-6">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                            Ship From
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                    Name</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $shipFromAddress['name'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                    Phone</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $shipFromAddress['phone'] ?? 'N/A' }}</p>
                                            </div>
                                            @if (!empty($shipFromAddress['company_name']))
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                        Company</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $shipFromAddress['company_name'] }}</p>
                                                </div>
                                            @endif
                                            @if (!empty($shipFromAddress['email']))
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                        Email</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $shipFromAddress['email'] }}</p>
                                                </div>
                                            @endif
                                            <div class="md:col-span-2">
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                    Complete Address</p>
                                                <div
                                                    class="text-sm text-gray-700 dark:text-gray-300 space-y-1 bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                                    <p class="font-medium">
                                                        {{ $shipFromAddress['address_line1'] ?? '' }}
                                                        @if (!empty($shipFromAddress['address_line2']))
                                                            {{ $shipFromAddress['address_line2'] }}
                                                        @endif
                                                    </p>
                                                    <p class="font-medium">
                                                        {{ $shipFromAddress['city_locality'] ? $shipFromAddress['city_locality'] . ', ' : '' }}
                                                        @if (!empty($shipFromAddress['state_province']))
                                                            {{ $shipFromAddress['state_province'] ? $shipFromAddress['state_province'] : '' }}
                                                        @endif
                                                        {{ $shipFromAddress['postal_code'] ? ' ' . $shipFromAddress['postal_code'] : '' }},
                                                        United States
                                                    </p>
                                                </div>
                                            </div>
                                            @if (!empty($shipFromAddress['address_residential_indicator']))
                                                <div class="md:col-span-2">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                        <i class="fas fa-home mr-1"></i>
                                                        Residential Address
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Ship To Section -->
                                    <div class="border-b pb-6">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                            <i class="fas fa-location-dot mr-2 text-green-600"></i>
                                            Ship To
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                    Name</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $shipToAddress['name'] ?? 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                    Phone</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $shipToAddress['phone'] ?? 'N/A' }}</p>
                                            </div>
                                            @if (!empty($shipToAddress['company_name']))
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                        Company</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $shipToAddress['company_name'] }}</p>
                                                </div>
                                            @endif
                                            @if (!empty($shipToAddress['email']))
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                                        Email</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $shipToAddress['email'] }}</p>
                                                </div>
                                            @endif
                                            <div class="md:col-span-2">
                                                <p
                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                    Complete Address</p>
                                                <div
                                                    class="text-sm text-gray-700 dark:text-gray-300 space-y-1 bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                                    <p class="font-medium">
                                                        {{ $shipToAddress['address_line1'] ?? 'N/A' }}
                                                        @if (!empty($shipToAddress['address_line2']))
                                                            {{ $shipToAddress['address_line2'] }}
                                                        @endif
                                                    </p>
                                                    <p class="font-medium">
                                                        {{ $shipToAddress['city_locality'] ? $shipToAddress['city_locality'] . ', ' : '' }}
                                                        @if (!empty($shipToAddress['state_province']))
                                                            {{ $shipToAddress['state_province'] ? $shipToAddress['state_province'] : '' }}
                                                        @endif
                                                        @if (!empty($shipToAddress['postal_code']))
                                                            {{ $shipToAddress['postal_code'] }},
                                                            {{ $shipToAddressCountryFullName ?? 'US' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            @if (!empty($shipToAddress['address_residential_indicator']))
                                                <div class="md:col-span-2">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                        <i class="fas fa-home mr-1"></i>
                                                        Residential Address
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Package Details Section -->
                                    <div>
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                            <i class="fas fa-box mr-2 text-purple-600"></i>
                                            Package Details
                                        </h3>
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-4">
                                            <!-- Package Type -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                        Package Type</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        @php
                                                            $selectedPackage = collect($carrierPackaging)->firstWhere(
                                                                'package_code',
                                                                $selectedPackaging,
                                                            );
                                                        @endphp
                                                        {{ $selectedPackage['name'] ?? 'N/A' }}
                                                    </p>
                                                </div>

                                                <!-- Weight -->
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                        Weight</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $package['weight'] ?? 'N/A' }} lbs
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Dimensions (if custom package) -->
                                            @if ($selectedPackage['package_code'] == 'custom' && !empty($package['length']))
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                        Dimensions (inches)</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ $package['length'] ?? 0 }} ×
                                                        {{ $package['width'] ?? 0 }} ×
                                                        {{ $package['height'] ?? 0 }}
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- Insurance -->
                                            @if (!empty($package['insured_value']) && $isInsuranceChecked)
                                                <div class="border-t pt-4">
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                        Insurance</p>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                                            <i class="fas fa-shield-alt mr-1"></i>
                                                            Declared Value:
                                                            ${{ number_format($package['insured_value'], 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Shipment Date -->
                                            @if (!empty($shipDate))
                                                <div class="border-t pt-4">
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                        Ship Date</p>
                                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $shipDate)->format('F d, Y') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Selected Label Preview -->
                                    @if ($selectedRate)
                                        <div class="border-t pt-6">
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                                <i class="fas fa-label mr-2 text-indigo-600"></i>
                                                Shipping Label Details
                                            </h3>
                                            <div
                                                class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 space-y-4 border border-indigo-200 dark:border-indigo-700">
                                                <!-- Service & Carrier -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <p
                                                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                                            Service Type</p>
                                                        <p class="text-base font-medium text-gray-900 dark:text-white">
                                                            {{ ucwords(str_replace('_', ' ', $selectedRate['service_type'] ?? 'N/A')) }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                                            Carrier</p>
                                                        <p class="text-base font-medium text-gray-900 dark:text-white">
                                                            {{ strtoupper($selectedRate['carrier_code'] ?? 'N/A') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Delivery Estimate -->
                                                @if (!empty($selectedRate['estimated_delivery_date']))
                                                    <div
                                                        class="border-t border-indigo-200 dark:border-indigo-700 pt-4">
                                                        <p
                                                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                                            Estimated Delivery</p>
                                                        <p class="text-base font-medium text-gray-900 dark:text-white">
                                                            {{ \Carbon\Carbon::parse($selectedRate['estimated_delivery_date'])->format('F d, Y') }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <!-- Price Breakdown -->
                                                <div class="border-t border-indigo-200 dark:border-indigo-700 pt-4">
                                                    <div class="space-y-2">
                                                        <!-- Shipping Amount -->
                                                        <div
                                                            class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                                            <span
                                                                class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                                                Shipping Amount:
                                                            </span>
                                                            @auth('customer')
                                                                <span
                                                                    class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                                                    ${{ number_format($end_user_total ?? 0, 2) }}
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                                                    ${{ $selectedRate['calculated_amount'] ?? 'N/A' }}
                                                                </span>
                                                            @endauth
                                                        </div>
                                                        <!-- Packaging Amount -->
                                                        <div
                                                            class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                                            <span
                                                                class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                                                Packaging Amount:
                                                            </span>
                                                            <span
                                                                class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                                                ${{ number_format($packagingAmount ?? 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <!-- Total Amount -->
                                                        <div
                                                            class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                                            <span
                                                                class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                                                Total Amount:
                                                            </span>
                                                            @auth('customer')
                                                                <span
                                                                    class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                                                    ${{ number_format($end_user_total ?? 0, 2) + number_format($packagingAmount ?? 0, 2) }}
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                                                    ${{ number_format(($selectedRate['calculated_amount'] ?? 0) + ($packagingAmount ?? 0), 2) }}
                                                                </span>
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Customs Section (International Only) -->
                                    @if ($shipToAddress['country_code'] != 'US' && !empty($customs['customs_items']))
                                        <div class="border-t pt-6">
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                                <i class="fas fa-file-invoice mr-2 text-orange-600"></i>
                                                Customs Information
                                            </h3>
                                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-4">
                                                <!-- General Customs Info -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4">
                                                    <div>
                                                        <p
                                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                            Contents Type</p>
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                                                            {{ ucfirst($customs['contents'] ?? 'N/A') }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                            Non-Delivery Action</p>
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                                            {{ ucfirst(str_replace('_', ' ', $customs['non_delivery'] ?? 'N/A')) }}
                                                        </p>
                                                    </div>
                                                    @if (!empty($customs['signer']))
                                                        <div>
                                                            <p
                                                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                                                Signed By</p>
                                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $customs['signer'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Customs Items -->
                                                <div>
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                                        Items ({{ count($customs['customs_items']) }})</p>
                                                    <div class="space-y-3">
                                                        @foreach ($customs['customs_items'] as $itemIndex => $item)
                                                            @if (!empty($item['description']))
                                                                <div
                                                                    class="border-l-4 border-orange-500 bg-white dark:bg-gray-700 rounded-r p-3 space-y-2">
                                                                    <div
                                                                        class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-gray-600 dark:text-gray-400">Description:</span>
                                                                            <p class="text-gray-900 dark:text-white">
                                                                                {{ $item['description'] ?? 'N/A' }}
                                                                            </p>
                                                                        </div>
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-gray-600 dark:text-gray-400">Quantity:</span>
                                                                            <p class="text-gray-900 dark:text-white">
                                                                                {{ $item['quantity'] ?? 'N/A' }}
                                                                            </p>
                                                                        </div>
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-gray-600 dark:text-gray-400">Value:</span>
                                                                            <p class="text-gray-900 dark:text-white">
                                                                                ${{ number_format($item['value']['amount'] ?? 0, 2) }}
                                                                                {{ strtoupper($item['value']['currency'] ?? 'USD') }}
                                                                            </p>
                                                                        </div>
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-gray-600 dark:text-gray-400">Weight:</span>
                                                                            <p class="text-gray-900 dark:text-white">
                                                                                {{ $item['weight']['value'] ?? 'N/A' }}
                                                                                {{ $item['weight']['unit'] ?? 'lbs' }}
                                                                            </p>
                                                                        </div>
                                                                        @if (!empty($item['harmonized_tariff_code']))
                                                                            <div>
                                                                                <span
                                                                                    class="font-medium text-gray-600 dark:text-gray-400">HS
                                                                                    Code:</span>
                                                                                <p
                                                                                    class="text-gray-900 dark:text-white">
                                                                                    {{ $item['harmonized_tariff_code'] }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-gray-600 dark:text-gray-400">Country
                                                                                of Origin:</span>
                                                                            <p class="text-gray-900 dark:text-white">
                                                                                {{ $item['country_of_origin'] ?? 'N/A' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Tax Identifiers -->
                                                @if (!empty($tax_identifiers) && count($tax_identifiers) > 0)
                                                    <div class="border-t pt-4">
                                                        <p
                                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                                            Tax Identifiers</p>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach ($tax_identifiers as $identifier)
                                                                @if (!empty($identifier['value']))
                                                                    <div class="bg-white dark:bg-gray-700 rounded p-2">
                                                                        <p
                                                                            class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                                                            {{ ucfirst(str_replace('_', ' ', $identifier['taxable_entity_type'])) }}
                                                                            ID
                                                                        </p>
                                                                        <p
                                                                            class="text-sm text-gray-900 dark:text-white font-mono">
                                                                            {{ $identifier['value'] }}</p>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex flex-col gap-3 mt-3">
                                        <x-checkbox wire:model.live="certifyHazardousMaterials"
                                            label="I certify that the shipment does not contain any undeclared hazardous materials (perfume, nail polish, hair spray, dry ice, lithium batteries, firearms, lighters, fuels, etc.) or any matter prohibited by law or postal regulation." />

                                        @if ($shipToAddress['country_code'] != 'US')
                                            <x-checkbox wire:model.live="certifyInvoiceAccuracy"
                                                label="I hereby certify that the information on this invoice is true and correct and the contents and value of this shipment is as stated above."
                                                required />
                                        @endif
                                    </div>

                                    {{-- Signature - Only show when certifications are complete --}}
                                    @if ($this->certificationsCompleted)
                                        <div class="mt-6" x-data="{ signatureReady: false }" x-init="setTimeout(() => {
                                            // Force a reflow to ensure DOM is ready
                                            document.body.offsetHeight;
                                            // Wait additional time for canvas initialization
                                            setTimeout(() => {
                                                signatureReady = true;
                                                // Trigger window resize to help signature component initialize
                                                $nextTick(() => {
                                                    window.dispatchEvent(new Event('resize'));
                                                });
                                            }, 200);
                                        }, 600)"
                                            wire:key="signature-{{ $certifyHazardousMaterials }}-{{ $certifyInvoiceAccuracy }}">
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                                <i class="fas fa-signature mr-2 text-gray-600"></i>
                                                Signature Confirmation
                                            </h3>

                                            <div x-show="signatureReady" x-transition>
                                                <x-signature wire:model="signature" label="Sign Below"
                                                    id="modal-signature" hint="Please sign in the box below" clearable
                                                    exportable color="#000000" background="#ffffff"
                                                    :height="200" />
                                            </div>

                                            <div x-show="!signatureReady"
                                                class="flex items-center justify-center py-12">
                                                <div
                                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600">
                                                </div>
                                                <span class="ml-3 text-gray-600">Initializing signature pad...</span>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                                                <div>
                                                    <h4 class="text-yellow-800 dark:text-yellow-100 font-medium">
                                                        Certification Required</h4>
                                                    <p class="text-yellow-700 dark:text-yellow-200 text-sm">
                                                        Please complete the certification requirements above to proceed
                                                        with signing.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <x-button wire:click="createLabel" color="green" class="w-full sm:w-auto"
                                        loading="createLabel">
                                        Proceed To Payment
                                    </x-button>
                                </div>
                            </x-modal>

                            <!-- Payment Modal -->
                            <x-modal wire="showPaymentModal" size="4xl" persistent>
                                <x-slot:title>
                                    💳 Payment for Shipping Label
                                </x-slot:title>
                                <div class="space-y-6">
                                    <!-- Payment Summary -->
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                        <h3
                                            class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">
                                            <i class="fas fa-receipt mr-2"></i>
                                            Payment Summary
                                        </h3>
                                        @if ($selectedRate)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p class="text-blue-700 dark:text-blue-300 font-medium">Service:
                                                    </p>
                                                    <p class="text-blue-900 dark:text-blue-100">
                                                        {{ $selectedRate['service_type'] ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-blue-700 dark:text-blue-300 font-medium">Carrier:
                                                    </p>
                                                    <p class="text-blue-900 dark:text-blue-100">
                                                        {{ $selectedRate['carrier_friendly_name'] ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-blue-700 dark:text-blue-300 font-medium">
                                                        Amount to Pay:
                                                    </p>
                                                    <p class="text-blue-900 dark:text-blue-100 font-bold text-lg">
                                                        @auth('customer')
                                                            ${{ number_format(($end_user_total ?? 0) + ($packagingAmount ?? 0), 2) }}
                                                        @else
                                                            ${{ number_format(($origin_total ?? 0) + ($packagingAmount ?? 0), 2) }}
                                                        @endauth
                                                    </p>
                                                    @if ($packagingAmount > 0)
                                                        <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">
                                                            (Shipping:
                                                            @auth('customer')
                                                                ${{ number_format($end_user_total ?? 0, 2) }}
                                                            @else
                                                                ${{ number_format($origin_total ?? 0, 2) }}
                                                            @endauth
                                                            + Packaging: ${{ number_format($packagingAmount, 2) }})
                                                        </p>
                                                    @endif
                                                </div>

                                            </div>
                                        @endif
                                    </div>

                                    <!-- Reader Selection -->
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <h4
                                            class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fas fa-credit-card mr-2 text-purple-600"></i>
                                            Select Terminal Reader
                                        </h4>
                                        @if (count($availableReaders) > 0)
                                            <div class="space-y-2">
                                                @foreach ($availableReaders as $reader)
                                                    <label
                                                        class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ $reader['status'] !== 'online' ? 'opacity-50' : '' }}">
                                                        <input type="radio" wire:model="selectedReaderId"
                                                            value="{{ $reader['id'] }}"
                                                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                                            {{ $reader['status'] !== 'online' ? 'disabled' : '' }}>
                                                        <div class="ml-3 flex-1">
                                                            <div class="flex items-center justify-between">
                                                                <p
                                                                    class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $reader['label'] ?? 'Unnamed Reader' }}
                                                                </p>
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                                    {{ $reader['status'] === 'online' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                                                    {{ $reader['status'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <i
                                                    class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                                <p class="text-gray-600 dark:text-gray-400">No readers found. Please
                                                    ensure your S700 reader is connected and registered.</p>
                                                <button wire:click="loadReaders"
                                                    class="mt-2 text-blue-600 hover:text-blue-700 text-sm">
                                                    <i class="fas fa-refresh mr-1"></i>
                                                    Refresh Readers
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Payment Status -->
                                    @if ($paymentProcessing)
                                        <div
                                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600 mr-3">
                                                </div>
                                                <div>
                                                    <h4 class="text-yellow-800 dark:text-yellow-100 font-medium">
                                                        @if ($paymentRetryCount > 0)
                                                            Retrying Payment... (Attempt {{ $paymentRetryCount + 1 }})
                                                        @else
                                                            Processing Payment...
                                                        @endif
                                                    </h4>
                                                    <p class="text-yellow-700 dark:text-yellow-200 text-sm">
                                                        Please present your test card to the S700 reader.
                                                        <strong>The amount should now be visible on your reader
                                                            screen.</strong>
                                                    </p>
                                                    @if ($paymentRetryCount > 0)
                                                        <p class="text-yellow-600 dark:text-yellow-300 text-xs mt-1">
                                                            Previous attempt failed - trying again now
                                                        </p>
                                                    @endif
                                                    @if ($paymentIntentId)
                                                        <p
                                                            class="text-yellow-600 dark:text-yellow-300 text-xs mt-1 font-mono">
                                                            Payment ID: {{ Str::limit($paymentIntentId, 20) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($paymentError)
                                        <div
                                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                                            <div class="flex items-start">
                                                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                                                <div class="flex-1">
                                                    <h4 class="text-red-800 dark:text-red-100 font-medium">Payment
                                                        Failed</h4>
                                                    <p class="text-red-700 dark:text-red-200 text-sm">
                                                        {{ $paymentError }}</p>
                                                    @if ($paymentRetryCount > 0)
                                                        <p class="text-red-600 dark:text-red-300 text-xs mt-1">
                                                            Attempt {{ $paymentRetryCount }} of
                                                            {{ $maxRetryAttempts }}
                                                        </p>
                                                    @endif
                                                    @if ($paymentRetryCount < $maxRetryAttempts)
                                                        <button wire:click="retryPayment" wire:loading="retryPayment"
                                                            wire:loading.class="cursor-wait"
                                                            wire:loading.attr="disabled"
                                                            class="mt-3 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                                                            <span wire:loading wire:target="retryPayment">
                                                                <i class="fas fa-spinner animate-spin mr-1"></i>
                                                                Retrying...
                                                            </span>
                                                            <span wire:loading.remove wire:target="retryPayment">
                                                                <i class="fas fa-redo mr-1"></i>
                                                                Retry Payment
                                                                ({{ $paymentRetryCount + 1 }}/{{ $maxRetryAttempts }})
                                                            </span>
                                                        </button>
                                                    @else
                                                        <div
                                                            class="mt-3 p-2 bg-red-100 dark:bg-red-900/30 rounded text-xs text-red-800 dark:text-red-200">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            Maximum retry attempts reached. Please close this modal and
                                                            try again.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($paymentSuccessful)
                                        <div
                                            class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                                <div>
                                                    <h4 class="text-green-800 dark:text-green-100 font-medium">Payment
                                                        Successful!</h4>
                                                    <p class="text-green-700 dark:text-green-200 text-sm">Creating your
                                                        shipping label now...</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <x-button wire:click="$set('showPaymentModal', false)" color="gray"
                                        :disabled="$paymentProcessing">
                                        Cancel
                                    </x-button>
                                    <x-button wire:click="processPayment" color="green" :disabled="!$selectedReaderId || $paymentProcessing || $paymentSuccessful"
                                        loading="processPayment">
                                        @if ($paymentProcessing)
                                            Processing...
                                        @else
                                            Pay & Create Label
                                        @endif
                                    </x-button>
                                </div>
                            </x-modal>

                            <x-button wire:click="backToCreateRatesPage" color="gray" class="w-full sm:w-auto"
                                loading="backToCreateRatesPage">
                                Back
                            </x-button>

                            <x-button wire:click="$toggle('showModal')" color="green" class="w-full sm:w-auto">
                                Review & Sign
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

        // Download Shipment Details PDF
        function downloadPDF(trackingNumber, signaturePath) {
            @this.downloadPDF(trackingNumber, signaturePath);
        }

        // Payment polling functionality
        let paymentPollInterval = null;
        let pollAttempts = 0;

        // Listen for payment processing start
        window.addEventListener('payment-processing-started', function() {
            if (paymentPollInterval) {
                clearInterval(paymentPollInterval);
            }

            pollAttempts = 0;

            // Give the reader time to display amount before starting to poll
            setTimeout(function() {
                // Start with 3-second intervals to give user time to present card
                paymentPollInterval = setInterval(function() {
                    pollAttempts++;
                    @this.pollPaymentStatus();

                    // After 15 attempts (45 seconds), slow down polling to every 5 seconds
                    if (pollAttempts >= 15) {
                        clearInterval(paymentPollInterval);
                        paymentPollInterval = setInterval(function() {
                            @this.pollPaymentStatus();
                        }, 5000);
                    }
                }, 3000); // Start with 3 second intervals
            }, 2000); // Wait 2 seconds before starting polling
        });

        // Listen for payment completion
        window.addEventListener('payment-completed', function() {
            if (paymentPollInterval) {
                clearInterval(paymentPollInterval);
                paymentPollInterval = null;
            }
        });

        // Listen for payment failed
        window.addEventListener('payment-failed', function() {
            if (paymentPollInterval) {
                clearInterval(paymentPollInterval);
                paymentPollInterval = null;
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (paymentPollInterval) {
                clearInterval(paymentPollInterval);
            }
        });
    </script>
</div>
