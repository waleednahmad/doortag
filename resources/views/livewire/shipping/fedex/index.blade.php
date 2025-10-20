<div x-data="fedexShippingForm()">
    <div>
        @if (!$hasResponse)
            <form wire:submit="getFedExQuote" @submit="if(window.showGlobalLoader) window.showGlobalLoader()"
                class="space-y-6 sm:space-y-8">
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg md:text-2xl font-semibold">
                            Create FedEx Shipping Label
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Get rates and create labels using FedEx services
                        </p>
                    </x-slot:header>

                    <!-- Ship From Section -->
                    <section class="mb-6">
                        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                            Ship From (Shipper)
                        </h2>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                <x-input label="Postal Code *" wire:model="shipper.postalCode" required />
                                {{-- <x-select.styled label="Country *" wire:model="shipper.countryCode" 
                                    :options="[
                                        ['value' => 'US', 'label' => 'United States'],
                                        ['value' => 'CA', 'label' => 'Canada'],
                                        ['value' => 'MX', 'label' => 'Mexico']
                                    ]" select="value:value|label:label" required /> --}}
                            </div>
                        </div>
                    </section>

                    <!-- Ship To Section -->
                    <section class="mb-6">
                        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                            Ship To (Recipient)
                        </h2>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                <x-input label="Postal Code *" wire:model="recipient.postalCode" required />
                                {{-- <x-select.styled label="Country *" wire:model="recipient.countryCode" 
                                    :options="[
                                        ['value' => 'US', 'label' => 'United States'],
                                        ['value' => 'CA', 'label' => 'Canada'],
                                        ['value' => 'MX', 'label' => 'Mexico']
                                    ]" select="value:value|label:label" required /> --}}
                            </div>
                        </div>
                    </section>

                    <!-- Shipment Options Section -->
                    <section class="mb-6">
                        <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-gray-200">
                            Shipment Options
                        </h2>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                <x-select.styled label="Pickup Type" wire:model="pickupType" 
                                    :options="$this->pickupTypes" select="value:value|label:label" />
                                <x-select.styled label="Service Type" wire:model="serviceType" 
                                    :options="$this->serviceTypes" select="value:value|label:label" />
                                <x-select.styled label="Preferred Currency" wire:model="preferredCurrency" 
                                    :options="$this->currencies" select="value:value|label:label" />
                                <x-input label="Account Number" wire:model="accountNumber" />
                            </div>
                        </div>
                    </section>

                    <!-- Package Details Section -->
                    <section class="mb-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
                                Package Details
                            </h2>
                            <x-button wire:click="addPackage" size="sm" color="primary" outline>
                                Add Package
                            </x-button>
                        </div>

                        @foreach ($requestedPackageLineItems as $index => $package)
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        Package {{ $index + 1 }}
                                    </h5>
                                    @if (count($requestedPackageLineItems) > 1)
                                        <x-button wire:click="removePackage({{ $index }})" size="sm" color="red" outline>
                                            Remove
                                        </x-button>
                                    @endif
                                </div>

                                <!-- Weight -->
                                <div class="mb-4">
                                    <h6 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weight</h6>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 max-w-md">
                                        <x-input label="Weight Value" type="number" step="0.1" min="0.1"
                                            wire:model="requestedPackageLineItems.{{ $index }}.weight.value" required />
                                        <x-select.styled label="Weight Unit" 
                                            wire:model="requestedPackageLineItems.{{ $index }}.weight.units"
                                            :options="[
                                                ['value' => 'LB', 'label' => 'Pounds (LB)'],
                                                ['value' => 'KG', 'label' => 'Kilograms (KG)']
                                            ]" select="value:value|label:label" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <x-button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 sm:px-8 sm:py-3 w-full sm:w-auto">
                            <span wire:loading.remove>Get FedEx Rates</span>
                            <span wire:loading>Getting Rates...</span>
                        </x-button>
                    </div>
                </x-card>
            </form>
        @endif

        <!-- Display Quotes Results -->
        @if ($hasResponse)
            <!-- Ship To Details Section -->
            <div class="mt-4 sm:mt-6">
                <h1 class="text-[30px] font-[700] text-gray-900 dark:text-white leading-[1.1] mb-[12px]">
                    FedEx Shipping to {{ $recipient['countryCode'] }}
                </h1>
                <div class="flex items-center gap-2 mb-[48px]">
                    <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
                        {{ $recipient['postalCode'] }} {{ $recipient['countryCode'] }}
                    </p>
                    <i class="fa-solid fa-paste text-[1em] text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition"
                        onclick="navigator.clipboard.writeText('{{ $recipient['postalCode'] }} {{ $recipient['countryCode'] }}')"
                        title="Copy to clipboard"></i>
                </div>
            </div>

            <!-- Shipment Details Section -->
            <div class="mb-[48px] py-[7px]" x-data="{ shipmentDetailsOpen: false }">
                <label @click="shipmentDetailsOpen = !shipmentDetailsOpen" class="flex cursor-pointer space-x-[5px]">
                    <div class="w-[20px] h-[20px] border-[2px] border-gray-500 dark:border-gray-400 rounded-[50%] flex items-center justify-center cursor-pointer">
                        <span x-show="!shipmentDetailsOpen" class="text-[12px] text-gray-500 dark:text-gray-400">+</span>
                        <span x-show="shipmentDetailsOpen" class="text-[12px] text-gray-500 dark:text-gray-400">-</span>
                    </div>
                    <p class="font-[500] text-[15px] text-gray-700 dark:text-gray-300">Shipment Details</p>
                </label>
                <div x-show="shipmentDetailsOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="p-[16px_16px_16px_24px] flex lg:flex-row flex-col justify-between bg-gray-100 dark:bg-gray-800 border-[2px] border-gray-300 dark:border-gray-600 rounded-[5px] mt-[10px] lg:gap-0 gap-[16px]">

                    <!-- Ship From Address -->
                    <div class="lg:w-[33.3333%] w-full text-[14px]">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Ship From Address</h1>
                        <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">{{ $shipper['postalCode'] }}</p>
                        <p class="text-gray-500 dark:text-gray-400 leading-[1.42857143]">{{ $shipper['countryCode'] }}</p>
                    </div>

                    <!-- Package Details -->
                    <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[8px] pl-0">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Package Details</h1>
                        @foreach ($requestedPackageLineItems as $index => $package)
                            <div class="mb-2">
                                <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                    Package {{ $index + 1 }}:
                                    <span class="pl-[4px] font-[400]">{{ $serviceType }}</span>
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                                    Weight:
                                    <span class="pl-[4px] font-[400]">
                                        {{ $package['weight']['value'] }} {{ $package['weight']['units'] }}
                                    </span>
                                </p>
                            </div>
                        @endforeach
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">Pickup Type: {{ str_replace('_', ' ', $pickupType) }}</p>
                    </div>

                    <!-- Service Details -->
                    <div class="lg:w-[33.3333%] w-full text-[14px] lg:pl-[16px] pl-0">
                        <h1 class="font-[500] text-gray-600 dark:text-gray-300 pb-[6px] leading-[1.42857143]">Service Details</h1>
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                            Service: <span class="pl-[4px] font-[400]">{{ str_replace('_', ' ', $serviceType) }}</span>
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                            Currency: <span class="pl-[4px] font-[400]">{{ $preferredCurrency }}</span>
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 font-[500] leading-[1.42857143]">
                            Account: <span class="pl-[4px] font-[400]">{{ $accountNumber }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <x-card class="mt-4 sm:mt-6">
                <x-slot:header>
                    <div class="flex flex-col space-y-3 lg:flex-row lg:justify-between lg:items-center lg:space-y-0">
                        <div>
                            <h3 class="text-lg sm:text-xl font-semibold">FedEx Rate Quotes</h3>
                            @if (!empty($quotes))
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Found {{ count($quotes) }} FedEx shipping option(s)
                                </p>
                            @endif
                        </div>
                    </div>
                </x-slot:header>

                @if (!empty($errorMessage))
                    <x-alert text="{{ $errorMessage }}" color="red" />
                @elseif(!empty($quotes))
                    <div class="space-y-4">
                        @foreach ($quotes as $index => $quote)
                            <div x-data="{ rateBreakdownOpen: false }" class="border rounded-lg overflow-hidden hover:shadow-md transition-all duration-300 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
                                <!-- Main Quote Content - Clickable -->
                                <div @click="rateBreakdownOpen = !rateBreakdownOpen" class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-3 sm:space-y-0">
                                        <div class="flex items-start space-x-3 flex-1">
                                            <!-- FedEx Logo -->
                                            <div class="flex-shrink-0 mt-1">
                                                <img src="{{ asset('assets/images/fedex.svg') }}" class="w-[55px] h-8 object-contain" alt="FedEx" />
                                            </div>

                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">
                                                    {{ $quote['serviceName'] ?? $quote['serviceType'] ?? 'FedEx Service' }}
                                                </h4>
                                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    Service Type: {{ str_replace('_', ' ', $quote['serviceType'] ?? 'Standard Service') }}
                                                </p>
                                                @if (isset($quote['packagingType']))
                                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                        Packaging: {{ str_replace('_', ' ', $quote['packagingType']) }}
                                                    </p>
                                                @endif
                                                @if (isset($quote['operationalDetail']['serviceCode']))
                                                    <p class="text-xs sm:text-sm text-blue-600 dark:text-blue-400">
                                                        Service Code: {{ $quote['operationalDetail']['serviceCode'] }}
                                                    </p>
                                                @endif
                                                
                                                <!-- Click to expand indicator -->
                                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <svg x-show="!rateBreakdownOpen" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                    <svg x-show="rateBreakdownOpen" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                    <span x-text="rateBreakdownOpen ? 'Hide details' : 'View rate breakdown'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Display both rate types if available -->
                                        <div class="text-center sm:text-right space-y-2">
                                            @if (isset($quote['ratedShipmentDetails']))
                                                @foreach ($quote['ratedShipmentDetails'] as $rateIndex => $rateDetail)
                                                    <div class="border rounded p-2 {{ $rateIndex === 0 ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600' }}">
                                                        <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1">
                                                            {{ $rateDetail['rateType'] === 'ACCOUNT' ? 'Account Rate' : 'Preferred Currency' }}
                                                        </div>
                                                        @if (isset($rateDetail['totalNetCharge']))
                                                            <div class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                                                {{ $rateDetail['currency'] ?? 'USD' }} ${{ number_format($rateDetail['totalNetCharge'] ?? 0, 2) }}
                                                            </div>
                                                            @if (isset($rateDetail['totalBaseCharge']) && $rateDetail['totalBaseCharge'] != $rateDetail['totalNetCharge'])
                                                                <div class="text-sm text-gray-500 dark:text-gray-400 line-through">
                                                                    Base: ${{ number_format($rateDetail['totalBaseCharge'] ?? 0, 2) }}
                                                                </div>
                                                            @endif
                                                            @if (isset($rateDetail['totalDiscounts']) && $rateDetail['totalDiscounts'] > 0)
                                                                <div class="text-sm text-green-600 dark:text-green-400">
                                                                    Discount: ${{ number_format($rateDetail['totalDiscounts'], 2) }}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced Rate Breakdown Section (Toggleable) -->
                                @if (isset($quote['ratedShipmentDetails'][0]['shipmentRateDetail']['surCharges']))
                                    <div x-show="rateBreakdownOpen" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-4"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 transform translate-y-0"
                                         x-transition:leave-end="opacity-0 transform -translate-y-4"
                                         class="bg-gray-50 dark:bg-gray-800/50 px-4 pb-4"
                                    >
                                        <div class="pt-4 border-t-2 border-gradient-to-r from-purple-200 to-blue-200 dark:from-purple-700 dark:to-blue-700">
                                        <!-- Enhanced Header -->
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full shadow-md">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h5 class="text-base font-bold text-gray-800 dark:text-gray-200">Rate Breakdown</h5>
                                        </div>

                                        @php
                                            $shipmentDetail = $quote['ratedShipmentDetails'][0]['shipmentRateDetail'];
                                        @endphp

                                        <!-- Enhanced Breakdown Content -->
                                        <div class="space-y-4">
                                            <!-- Base Charges Section -->
                                            <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/20 dark:via-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-green-200 dark:border-green-700/50">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="flex items-center justify-center w-6 h-6 bg-green-500 rounded-full">
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider">Base Shipping Rate</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Standard Rate</span>
                                                    <div class="bg-white dark:bg-gray-700 px-3 py-1.5 rounded-lg shadow-sm border border-green-300 dark:border-green-600">
                                                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                                            ${{ number_format($quote['ratedShipmentDetails'][0]['totalBaseCharge'] ?? 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Surcharges Section -->
                                            @if (!empty($shipmentDetail['surCharges']))
                                                <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-yellow-50 dark:from-orange-900/20 dark:via-amber-900/20 dark:to-yellow-900/20 rounded-xl p-4 border border-orange-200 dark:border-orange-700/50">
                                                    <div class="flex items-center gap-2 mb-3">
                                                        <div class="flex items-center justify-center w-6 h-6 bg-orange-500 rounded-full">
                                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase tracking-wider">Additional Fees</span>
                                                    </div>
                                                    <div class="space-y-3">
                                                        @foreach ($shipmentDetail['surCharges'] as $surcharge)
                                                            <div class="flex justify-between items-center bg-white/60 dark:bg-gray-700/60 rounded-lg p-3 border border-orange-200/50 dark:border-orange-600/30">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ $surcharge['description'] ?? 'Additional Fee' }}
                                                                    </span>
                                                                </div>
                                                                <span class="text-sm font-bold text-orange-600 dark:text-orange-400 bg-white dark:bg-gray-700 px-2 py-1 rounded border border-orange-300 dark:border-orange-600">
                                                                    ${{ number_format($surcharge['amount'] ?? 0, 2) }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Shipping Details Section -->
                                            <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700/50">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-full">
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Shipment Details</span>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <!-- Weight Info -->
                                                    @if (isset($shipmentDetail['totalBillingWeight']))
                                                        <div class="bg-white/70 dark:bg-gray-700/70 rounded-lg p-3 border border-blue-200/50 dark:border-blue-600/30">
                                                            <div class="flex justify-between items-center">
                                                                <div class="flex items-center gap-2">
                                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-3m-3 3l-3-3"></path>
                                                                    </svg>
                                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Weight</span>
                                                                </div>
                                                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                                                    {{ $shipmentDetail['totalBillingWeight']['value'] }} {{ $shipmentDetail['totalBillingWeight']['units'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Rate Zone -->
                                                    @if (isset($shipmentDetail['rateZone']))
                                                        <div class="bg-white/70 dark:bg-gray-700/70 rounded-lg p-3 border border-blue-200/50 dark:border-blue-600/30">
                                                            <div class="flex justify-between items-center">
                                                                <div class="flex items-center gap-2">
                                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Zone</span>
                                                                </div>
                                                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded">
                                                                    {{ $shipmentDetail['rateZone'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Service Details -->
                                @if (isset($quote['serviceDescription']['description']))
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex flex-wrap items-center gap-2 text-xs ps-4 pb-4">
                                            <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-2 py-1 rounded">
                                                {{ $quote['serviceDescription']['description'] }}
                                            </span>
                                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded">
                                                {{ $quote['serviceDescription']['serviceCategory'] ?? 'parcel' }}
                                            </span>
                                            @if (isset($quote['signatureOptionType']))
                                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded">
                                                    {{ str_replace('_', ' ', $quote['signatureOptionType']) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">📦</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                            No FedEx quotes available
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Please check your shipping details and try again.
                        </p>
                    </div>
                @endif
            </x-card>
        @endif
    </div>

    <script>
        function fedexShippingForm() {
            return {
                // Add any custom Alpine.js logic here if needed
            }
        }
    </script>
</div>