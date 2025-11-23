<div class="space-y-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-[30px] font-[700] text-gray-900 dark:text-white leading-[1.1] mb-[12px]">
            My Shipments
        </h1>
        <p class="text-[17px] text-gray-700 dark:text-gray-300 leading-[1.42857143] font-[500]">
            View and manage your shipping labels and tracking information
        </p>
    </div>

    <!-- Shipments Grid -->
    @if (count($this->labels) > 0)
        <x-card class="mt-4 sm:mt-6">
            <div class="space-y-4 flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 sm:pb-2">
                <!-- Title and Count -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                            Shipment History
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $this->totalLabels }} {{ Str::plural('shipment', $this->totalLabels) }}
                            found
                            @if ($this->totalPages > 1)
                                (Page {{ $this->currentPage }} of {{ $this->totalPages }})
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Pagination Controls Top -->
                <div class="flex flex-wrap gap-2 items-center justify-end">
                    @if ($this->totalPages > 1)
                        <x-button wire:click="previousPage" loading="previousPage" color="gray" sm :disabled="$this->currentPage <= 1"
                            title="Previous page">
                            <x-slot:left>
                                <i class="fas fa-chevron-left mr-1"></i>
                            </x-slot:left>
                            Previous
                        </x-button>

                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Page {{ $this->currentPage }} / {{ $this->totalPages }}
                        </span>

                        <x-button wire:click="nextPage" loading="nextPage" color="gray" sm :disabled="$this->currentPage >= $this->totalPages"
                            title="Next page">
                            Next
                            <x-slot:right>
                                <i class="fas fa-chevron-right ml-1"></i>
                            </x-slot:right>
                        </x-button>
                    @endif

                    <x-button wire:click="refreshLabels" sm loading="refreshLabels" title="Refresh labels"
                        color='sky' light>
                        <x-slot:left>
                            <i class="fas fa-sync-alt mr-1"></i>
                        </x-slot:left>
                        Refresh
                    </x-button>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($this->labels as $index => $label)
                    @php
                        $packages = $label['packages'] ?? [];
                        $shipTo = $label['ship_to'] ?? [];
                        $cost =
                            [
                                'origin_total' => $label['origin_total'] ?? 0,
                                'customer_total' => $label['customer_total'] ?? 0,
                                'end_user_total' => $label['end_user_total'] ?? 0,
                            ] ?? [];
                        $isVoided = $label['status'] === 'voided';
                        $orderNumber = $loop->iteration + $this->perPage * ($this->currentPage - 1);
                    @endphp

                    <div x-data="{ open: false }"
                        class="border rounded-lg overflow-hidden hover:shadow-md transition-all duration-300 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
                        <!-- Main Shipment Content -->
                        <div class="p-4 sm:p-6">
                            <!-- Header Row -->
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                                <!-- Left: Shipment Info -->
                                <div class="flex-1 mb-3 lg:mb-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                                        <!-- Order Number Counter -->
                                        <div class="mb-2 sm:mb-0">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                <i class="fas fa-hashtag mr-2"></i>
                                                {{ $orderNumber }}
                                            </span>
                                        </div>

                                        <!-- Database ID -->
                                        <div class="mb-2 sm:mb-0">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                <i class="fas fa-database mr-2"></i>
                                                Order: {{ $label['id'] ?? 'N/A' }}
                                            </span>
                                        </div>

                                        <!-- Tracking Number (Hidden if voided) -->
                                        @if (!$isVoided)
                                            <div class="mb-2 sm:mb-0">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 cursor-pointer hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                                    onclick="copyTrackingNumber('{{ $label['tracking_number'] ?? 'N/A' }}')"
                                                    title="Click to copy tracking number">
                                                    <i class="fas fa-truck mr-2"></i>
                                                    {{ $label['tracking_number'] ?? 'N/A' }}
                                                    <i class="fas fa-copy ml-2 text-xs opacity-70"></i>
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="mb-2 sm:mb-0">
                                            @php
                                                $status = $label['status'] ?? 'unknown';
                                                $badgeClass = match ($status) {
                                                    'completed'
                                                        => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'pending'
                                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'cancelled'
                                                        => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'voided'
                                                        => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    default
                                                        => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </div>

                                        <!-- Tracking Status (Hidden if voided) -->
                                        @if (!$isVoided)
                                            @php
                                                $trackingStatus = $label['tracking_status'] ?? 'unknown';
                                            @endphp
                                            @if ($trackingStatus !== 'unknown')
                                                <div class="mb-2 sm:mb-0">
                                                    @php
                                                        $trackingBadgeClass = match ($trackingStatus) {
                                                            'delivered'
                                                                => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                            'in_transit'
                                                                => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                            'out_for_delivery'
                                                                => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                            'exception'
                                                                => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                            default
                                                                => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $trackingBadgeClass }}">
                                                        {{ ucwords(str_replace('_', ' ', $trackingStatus)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Right: Amount and Actions -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4 lg:justify-end">
                                    @if (!empty($label['estimated_delivery_date']))
                                        <div class="mb-2 sm:mb-0">
                                            <div class="text-left">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Estimated Delivery
                                                </p>
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($label['estimated_delivery_date'])->format('l m/d') }}
                                                    by
                                                    {{ \Carbon\Carbon::parse($label['estimated_delivery_date'])->format('h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        @elseif (!empty($label['carrier_delivery_days']))
                                        <div class="mb-2 sm:mb-0">
                                            <div class="text-left">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Estimated Delivery
                                                </p>
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ $label['carrier_delivery_days'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Amount Display -->
                                    @if (!empty($cost))
                                        <div class="mb-2 sm:mb-0">
                                            <div class="text-left">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    ${{ number_format($label['stripe_amount_paid'] ?? 0, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif


                                    <!-- Collapse Toggle Button -->
                                    <button @click="open = !open"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600"
                                        title="Toggle Details">
                                        <i class="fas mr-2" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        <span x-text="open ? 'Hide Details' : 'Show Details'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Collapsible Details Section -->
                            <div x-show="open" x-collapse>
                                <!-- Action Buttons -->
                                <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex flex-wrap gap-2">
                                        {{-- void a label --}}
                                        @if ($label['status'] != 'voided')
                                            <x-button wire:click="confirmVoidLabel('{{ $label['label_id'] ?? '' }}' )"
                                                loading="voidLabel('{{ $label['label_id'] ?? '' }}' ) " 
                                                color="red"
                                                sm title="Void Label">
                                                <x-slot:left>
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                </x-slot:left>
                                                Void
                                            </x-button>
                                        @endif

                                        @if (!empty($label['tracking_url']) && !$isVoided)
                                            <button wire:click="redirectToTracking('{{ $label['tracking_url'] }}')"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                title="Track Shipment">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                Track
                                            </button>
                                        @endif

                                        <a href="{{ route('shipments.receipt', $label['label_id']) }}" target="_blank"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-teal-600 border border-transparent rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Print Receipt">
                                            <i class="fas fa-print mr-1"></i>
                                            Print Receipt
                                        </a>

                                        @if (isset($label['label_download']['png']))
                                            <button
                                                onclick="window.open('{{ $label['label_download']['png'] }}', '_blank')"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600"
                                                title="View PNG Label">
                                                <i class="fas fa-image mr-1"></i>
                                                Label PNG
                                            </button>
                                        @endif

                                        @if (isset($label['label_download']['pdf']))
                                            <button
                                                onclick="window.open('{{ $label['label_download']['pdf'] }}', '_blank')"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                title="View PDF Label">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                Label PDF
                                            </button>
                                        @endif

                                        @if (isset($label['form_download']['href']))
                                            <button
                                                onclick="window.open('{{ $label['form_download']['href'] }}', '_blank')"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                title="View Customs Form">
                                                <i class="fas fa-file-alt mr-1"></i>
                                                Form
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Shipment Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                    <!-- Ship To -->
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Ship To</h4>
                                        <div class="text-gray-600 dark:text-gray-400 space-y-1">
                                            <div class="text-xs text-gray-500 dark:text-gray-500">Name</div>
                                            <p class="font-medium">{{ $shipTo['name'] ?? 'N/A' }}</p>

                                            @if (!empty($shipTo['company_name']))
                                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Company
                                                </div>
                                                <p>{{ $shipTo['company_name'] }}</p>
                                            @endif

                                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Address</div>
                                            <p>
                                                {{ $shipTo['address_line1'] ?? 'N/A' }}
                                                @if (!empty($shipTo['address_line2']))
                                                    {{ $shipTo['address_line2'] }}
                                                @endif
                                            </p>
                                            <p>
                                                {{ $shipTo['city_locality'] ? $shipTo['city_locality'] . ', ' : '' }}
                                                @if (!empty($shipTo['state_province']))
                                                    {{ $shipTo['state_province'] }}
                                                @endif
                                                {{ $shipTo['postal_code'] ?? '' }},
                                                {{ $label['ship_to_address_country_full_name'] ?? ($shipTo['country_code'] ?? 'United States') }}
                                            </p>

                                            @if (isset($shipTo['address_residential_indicator']) && strtolower($shipTo['address_residential_indicator']) === 'yes')
                                                <span
                                                    class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                                    Residential Address
                                                </span>
                                            @else
                                                <span
                                                    class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded">
                                                    Business Address
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Package Info -->
                                    @if (!empty($packages) && is_array($packages))
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                                                {{ count($packages) > 1 ? 'Package Details (' . count($packages) . ')' : 'Package Details' }}
                                            </h4>
                                            <div class="text-gray-600 dark:text-gray-400 space-y-3">
                                                @foreach ($packages as $pkgIndex => $package)
                                                    <div class="{{ $pkgIndex > 0 ? 'pt-3 border-t border-gray-200 dark:border-gray-600' : '' }}">
                                                        @if (count($packages) > 1)
                                                            <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Package {{ $pkgIndex + 1 }}</p>
                                                        @endif
                                                        <p><span class="font-medium">Weight:</span>
                                                            {{ $package['weight']['value'] ?? 0 }}
                                                            {{ $package['weight']['unit'] ?? 'lb' }}</p>
                                                        @if (isset($package['dimensions']))
                                                            <p><span class="font-medium">Dimensions:</span>
                                                                {{ $package['dimensions']['length'] ?? 0 }} x
                                                                {{ $package['dimensions']['width'] ?? 0 }} x
                                                                {{ $package['dimensions']['height'] ?? 0 }}
                                                                {{ $package['dimensions']['unit'] ?? 'in' }}
                                                            </p>
                                                        @endif
                                                        <p><span class="font-medium">Package:</span>
                                                            {{ ucfirst($package['package_code'] ?? 'package') }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Service Info -->
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Service Details</h4>
                                        <div class="text-gray-600 dark:text-gray-400">
                                            <p><span class="font-medium">Carrier:</span>
                                                {{ strtoupper($label['carrier_code'] ?? 'N/A') }}</p>
                                            <p><span class="font-medium">Service:</span>
                                                {{ ucwords(str_replace('_', ' ', $label['service_code'] ?? 'N/A')) }}
                                            </p>
                                            <p><span class="font-medium">Ship Date:</span>
                                                {{ isset($label['ship_date']) ? \Carbon\Carbon::parse($label['ship_date'])->format('M j, Y') : 'N/A' }}
                                            </p>
                                            <p><span class="font-medium">Created:</span>
                                                {{ isset($label['created_at']) ? \Carbon\Carbon::parse($label['created_at'])->setTimezone('America/New_York')->format('M j, Y g:i A') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            @if ($this->totalPages > 1)
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Left: Info -->
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing page <span class="font-medium">{{ $this->currentPage }}</span> of
                            <span class="font-medium">{{ $this->totalPages }}</span>
                            ({{ $this->totalLabels }} total {{ Str::plural('shipment', $this->totalLabels) }})
                        </div>

                        <!-- Right: Navigation -->
                        <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
                            <x-button wire:click="previousPage" loading="previousPage" color="gray" sm
                                :disabled="$this->currentPage <= 1" title="Previous page">
                                <slot:left>
                                    <i class="fas fa-chevron-left mr-2"></i>
                                </slot:left>
                                Previous
                            </x-button>

                            <!-- Page Numbers -->
                            <div class="flex gap-1 items-center">
                                @for ($i = 1; $i <= $this->totalPages; $i++)
                                    @if ($i == $this->currentPage)
                                        <x-button loading="goToPage({{ $i }})" color="blue" sm>
                                            {{ $i }}
                                        </x-button>
                                    @elseif ($i <= 2 || $i > $this->totalPages - 2 || ($i >= $this->currentPage - 1 && $i <= $this->currentPage + 1))
                                        <x-button wire:click="goToPage({{ $i }})" sm
                                            loading="goToPage({{ $i }})">
                                            {{ $i }}
                                        </x-button>
                                    @elseif ($i == 3 && $this->currentPage > 4)
                                        <span class="px-2 text-gray-500 dark:text-gray-400">...</span>
                                    @endif
                                @endfor
                            </div>

                            <x-button wire:click="nextPage" loading="nextPage" color="gray" sm :disabled="$this->currentPage >= $this->totalPages"
                                title="Next page">
                                Next
                                <i class="fas fa-chevron-right ml-2"></i>
                            </x-button>
                        </div>
                    </div>
                </div>
            @endif
        </x-card>
    @else
        <!-- Empty State -->
        <x-card class="mt-4 sm:mt-6">
            <div class="text-center py-12">
                <div class="text-gray-500 dark:text-gray-400 mb-4">
                    <i class="fas fa-shipping-fast text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No shipments found
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    You haven't created any shipping labels yet.
                </p>
                <a href="{{ route('shipping.shipengine.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Create Shipment
                </a>
            </div>
        </x-card>
    @endif
</div>

<script>
    function copyTrackingNumber(trackingNumber) {
        // Copy to clipboard
        navigator.clipboard.writeText(trackingNumber).then(function() {
            // Trigger Livewire method for toast
            @this.trackingNumberCopied();
        }).catch(function(err) {
            // Fallback for older browsers
            const textArea = document.createElement("textarea");
            textArea.value = trackingNumber;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            // Trigger Livewire method for toast
            @this.trackingNumberCopied();
        });
    }

    // Listen for redirect to tracking event
    window.addEventListener('redirect-to-tracking', event => {
        const url = event.detail.url;
        window.open(url, '_blank');
    });

    // Listen for open receipt event
    window.addEventListener('open-receipt', event => {
        const labelId = event.detail.labelId;
        const receiptUrl = "{{ route('shipments.receipt', ['labelId' => ':labelId']) }}".replace(':labelId',
            labelId);
        window.open(receiptUrl, '_blank');
    });
</script>
