@props([
    'shipToAddress' => [],
    'shipFromAddress' => [],
    'selectedRate' => null,
    'selectedPackage' => null,
    'package' => [],
    'customs' => [],
    'certifyHazardousMaterials' => false,
    'certifyInvoiceAccuracy' => false,
    'certificationsCompleted' => false,
    'carrierPackaging' => [],
    'selectedPackaging' => '',
    'isInsuranceChecked' => false,
    'endUserTotal' => 0,
    'packagingAmount' => 0,
])

<x-modal scrollable wire="showModal" size="4xl"
    x-on:open="$wire.set('signature', null); setTimeout(() => { window.dispatchEvent(new Event('resize')) }, 300), $focusOn('modal-signature')">
    <x-slot:title>
        Shipment Details Review
    </x-slot:title>
    <div id="modal-content" class="space-y-6">
        <!-- Ship From Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                Ship From
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Name</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $shipFromAddress['name'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Phone</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $shipFromAddress['phone'] ?? 'N/A' }}</p>
                </div>
                @if (!empty($shipFromAddress['company_name']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Company</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $shipFromAddress['company_name'] }}</p>
                    </div>
                @endif
                @if (!empty($shipFromAddress['email']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Email</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $shipFromAddress['email'] }}</p>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                @dump($shipFromAddress['address_residential_indicator'])
                @if (
                    !empty($shipFromAddress['address_residential_indicator']) &&
                        ($shipFromAddress['address_residential_indicator'] === 'yes' ||
                            $shipFromAddress['address_residential_indicator'] === true))
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-location-dot mr-2 text-green-600"></i>
                Ship To
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Name</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $shipToAddress['name'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Phone</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $shipToAddress['phone'] ?? 'N/A' }}</p>
                </div>
                @if (!empty($shipToAddress['company_name']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Company</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $shipToAddress['company_name'] }}</p>
                    </div>
                @endif
                @if (!empty($shipToAddress['email']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Email</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $shipToAddress['email'] }}</p>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                @if (
                    !empty($shipToAddress['address_residential_indicator']) &&
                        ($shipToAddress['address_residential_indicator'] === 'yes' ||
                            $shipToAddress['address_residential_indicator'] === true))
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-box mr-2 text-purple-600"></i>
                Package Details
            </h3>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-4">
                <!-- Package Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Weight</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $package['weight'] ?? 'N/A' }} lbs
                        </p>
                    </div>
                </div>

                <!-- Dimensions (if custom package) -->
                @if ($selectedPackage['package_code'] == 'custom' && !empty($package['length']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
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
                        <div class="border-t border-indigo-200 dark:border-indigo-700 pt-4">
                            <p
                                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                Estimated Delivery
                            </p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($selectedRate['estimated_delivery_date'])->format('l m/d') }}
                                by
                                {{ \Carbon\Carbon::parse($selectedRate['estimated_delivery_date'])->format('h:i A') }}
                            </p>
                        </div>
                    @elseif (!empty($selectedRate['carrier_delivery_days']))
                        <div class="border-t border-indigo-200 dark:border-indigo-700 pt-4">
                            <p
                                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                                Estimated Delivery
                            </p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $selectedRate['carrier_delivery_days'] }}
                            </p>
                        </div>
                    @endif

                    <!-- Price Breakdown -->
                    <div class="border-t border-indigo-200 dark:border-indigo-700 pt-4">
                        <div class="space-y-2">
                            <!-- Shipping Amount -->
                            <div
                                class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                    Shipping Amount:
                                </span>
                                @auth('customer')
                                    <span class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                        ${{ number_format($endUserTotal ?? 0, 2) }}
                                    </span>
                                @else
                                    <span class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                        ${{ $selectedRate['calculated_amount'] ?? 'N/A' }}
                                    </span>
                                @endauth
                            </div>
                            <!-- Packaging Amount -->
                            <div
                                class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                    Packaging Amount:
                                </span>
                                <span class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                    ${{ number_format($packagingAmount ?? 0, 2) }}
                                </span>
                            </div>
                            <!-- Total Amount -->
                            <div
                                class="flex justify-between items-center border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                    Total Amount:
                                </span>
                                @auth('customer')
                                    <span class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                                        ${{ number_format($endUserTotal ?? 0, 2) + number_format($packagingAmount ?? 0, 2) }}
                                    </span>
                                @else
                                    <span class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
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
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            Items ({{ count($customs['customs_items']) }})</p>
                        <div class="space-y-3">
                            @foreach ($customs['customs_items'] as $itemIndex => $item)
                                @if (!empty($item['description']))
                                    <div
                                        class="border-l-4 border-orange-500 bg-white dark:bg-gray-700 rounded-r p-3 space-y-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
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
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">HS
                                                        Code:</span>
                                                    <p class="text-gray-900 dark:text-white">
                                                        {{ $item['harmonized_tariff_code'] }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Country
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
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                Tax Identifiers</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($tax_identifiers as $identifier)
                                    @if (!empty($identifier['value']))
                                        <div class="bg-white dark:bg-gray-700 rounded p-2">
                                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                                {{ ucfirst(str_replace('_', ' ', $identifier['taxable_entity_type'])) }}
                                                ID
                                            </p>
                                            <p class="text-sm text-gray-900 dark:text-white font-mono">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-signature mr-2 text-gray-600"></i>
                    Signature Confirmation
                </h3>

                <div x-show="signatureReady" x-transition>
                    <x-signature wire:model="signature" label="Sign Below" id="modal-signature"
                        hint="Please sign in the box below" clearable exportable color="#000000" background="#ffffff"
                        :height="200" />
                </div>

                <div x-show="!signatureReady" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600">
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
        <x-button wire:click="createLabel" color="green" class="w-full sm:w-auto" loading="createLabel">
            Proceed To Payment
        </x-button>
    </div>
</x-modal>
