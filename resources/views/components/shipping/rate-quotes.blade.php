@props([
    'rates' => [],
    'selectedRate' => null,
    'sortBy' => 'price',
    'sortDirection' => 'asc',
    'carriers' => [],
    'selectedCarrier' => '',
    'package' => [],
    'packagingAmount' => 0,
])

@php
    // Filter rates to show only primary carrier (se-4121981)
    $primaryCarrierId = 'se-4121981';
    $primaryRates = collect($rates)->filter(function ($rate) use ($primaryCarrierId) {
        return $rate['carrier_id'] === $primaryCarrierId;
    })->values()->all();
@endphp

@if (!empty($primaryRates))
    <div class="space-y-4">
        @foreach ($primaryRates as $index => $rate)
            <div x-data="{ rateBreakdownOpen: false }"
                class="border rounded-lg overflow-hidden hover:shadow-md transition-all duration-300 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
                <!-- Main Quote Content - Clickable -->
                <div @click="rateBreakdownOpen = !rateBreakdownOpen"
                    wire:click.stop="selectRate('{{ $rate['rate_id'] }}')"
                    class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-3 sm:space-y-0">
                        <div class="flex items-center space-x-3 flex-1">
                            <!-- Carrier Logo -->
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-900/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-truck text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <!-- Rate Info -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ collect($carriers)->firstWhere('carrier_id', $rate['carrier_id'])['friendly_name'] ?? 'Unknown Carrier' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $rate['service_type'] ?? 'Standard' }} • 
                                    @if ($rate['estimated_delivery_date'])
                                        Arrives by {{ \Carbon\Carbon::parse($rate['estimated_delivery_date'])->format('M d, Y') }}
                                    @else
                                        {{ $rate['delivery_days'] ?? '—' }} days
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Rate Details with Price Comparison -->
                        <div class="text-center sm:text-right space-y-2">
                            <div class="flex items-center justify-end space-x-2 flex-wrap">
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($rate['shipping_amount']['amount'], 2) }}
                                </span>
                                @if ($selectedRate && $selectedRate['rate_id'] === $rate['rate_id'])
                                    <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded">
                                        Selected
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Price Comparison Badge -->
                            @if (isset($rate['price_comparison']) && $rate['price_comparison']['is_cheaper'] !== null)
                                @php
                                    $comparison = $rate['price_comparison'];
                                    $priceDiff = $comparison['price_difference'];
                                    $percentDiff = $comparison['difference_percentage'];
                                @endphp
                                
                                @if ($comparison['is_cheaper'] === 'carrier_1')
                                    <div class="inline-flex items-center space-x-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-xs font-medium">
                                        <i class="fas fa-arrow-down"></i>
                                        <span>${{ number_format($priceDiff, 2) }} cheaper than alternative</span>
                                        @if ($percentDiff !== null && $percentDiff != 0)
                                            <span>({{ abs($percentDiff) }}% lower)</span>
                                        @endif
                                    </div>
                                @elseif ($comparison['is_cheaper'] === 'carrier_2')
                                    <div class="inline-flex items-center space-x-1 px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-lg text-xs font-medium">
                                        <i class="fas fa-arrow-up"></i>
                                        <span>${{ number_format($priceDiff, 2) }} more expensive</span>
                                        @if ($percentDiff !== null && $percentDiff != 0)
                                            <span>({{ abs($percentDiff) }}% higher)</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="inline-flex items-center space-x-1 px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium">
                                        <i class="fas fa-equals"></i>
                                        <span>Same price as alternative</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if (isset($rate['warning_messages']) && count($rate['warning_messages']) > 0)
                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                {{ $rate['warning_messages'][0] }}
                            </p>
                        </div>
                    @endif
                    @if (isset($rate['error_messages']) && count($rate['error_messages']) > 0)
                        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded">
                            <p class="text-sm text-red-800 dark:text-red-200">
                                {{ $rate['error_messages'][0] }}
                            </p>
                        </div>
                    @endif

                    <!-- Rate Actions -->
                    <div class="mt-3 flex justify-between items-center">
                        <button wire:click.stop="selectRate('{{ $rate['rate_id'] }}')"
                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section for the $packagingAmount --}}
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between">
        <div class="text-lg font-bold text-gray-900 dark:text-white">
            <x-number wire:model="packagingAmount" label="Packaging Amount" step="0.1" min="0" required />
        </div>
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
