<div>
    <div>
        <x-card  wire:show="!hasResponse">
            <x-slot:header wire:show="!hasResponse">
                <h3 class="text-lg font-semibold">Create Shipping Quote</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Get instant shipping rates</p>
            </x-slot:header>

            <form wire:submit="getQuote" class="space-y-8">
                <!-- Ship From Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="text-md font-semibold mb-4 text-gray-800 dark:text-gray-200">Ship From</h4>

                    <!-- Sender Details -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-gray-200">Sender Information</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Origin address details</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-select.styled label="Country" searchable wire:model="sender.country" :options="$this->countries"
                                placeholder="Select country" required />

                            <x-input label="ZIP Code" wire:model="sender.zip" placeholder="e.g., 84117" required />
                        </div>
                    </div>
                </div>

                <!-- Ship To Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="text-md font-semibold mb-4 text-gray-800 dark:text-gray-200">Ship To</h4>

                    <!-- Receiver Details -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-gray-200">Receiver Information</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Destination address details</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input label="City" wire:model="receiver.city" placeholder="e.g., Toronto" required />

                            <x-select.styled label="Country" wire:model="receiver.country" :options="$this->countries" searchable
                                placeholder="Select country" required />

                            <x-input label="ZIP Code" wire:model="receiver.zip" placeholder="e.g., M9C5K5" required />

                            <x-input label="Email" type="email" wire:model="receiver.email" placeholder="foo@bar.com"
                                required />
                        </div>
                    </div>
                </div>

                <!-- Type of Packaging Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="text-md font-semibold mb-4 text-gray-800 dark:text-gray-200">Type of Packaging</h4>

                    <!-- Package Type Display -->
                    <div
                        class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-4 border-2 border-blue-200 dark:border-blue-600">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-16 h-16 bg-orange-200 dark:bg-orange-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-orange-600 dark:text-orange-300" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-800 dark:text-gray-200">Box or Rigid Packaging</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Any custom box or thick parcel</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Items Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200">Package Items</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Add dimensions and weight for each
                                package</p>
                        </div>
                        <x-button wire:click="addItem" wire:loading.attr="disabled" wire:target="addItem"
                            class="px-4 py-2 rounded-lg font-medium">
                            <span wire:loading.remove wire:target="addItem">+ Add Item</span>
                            <span wire:loading wire:target="addItem">Adding...</span>
                        </x-button>
                    </div>

                    @foreach ($pieces as $index => $piece)
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-6 mb-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center mb-6">
                                <h5 class="font-medium text-gray-800 dark:text-gray-200">Package {{ $index + 1 }}
                                </h5>
                                @if (count($pieces) > 1)
                                    <x-button wire:click="removeItem({{ $index }})" wire:loading.attr="disabled"
                                        wire:target="removeItem({{ $index }})"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                        <span wire:loading.remove
                                            wire:target="removeItem({{ $index }})">Remove</span>
                                        <span wire:loading
                                            wire:target="removeItem({{ $index }})">Removing...</span>
                                    </x-button>
                                @endif
                            </div>

                            <!-- Package Dimensions -->
                            <div class="mb-8">
                                <h6 class="text-base font-medium text-gray-800 dark:text-gray-200 mb-4">Package
                                    Dimensions (Inches)</h6>
                                <div class="grid grid-cols-5 gap-4 items-end">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Length</label>
                                        <input type="number" wire:model="pieces.{{ $index }}.length"
                                            class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="5.1" step="0.01" required>
                                    </div>
                                    <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                        <span class="text-xl">×</span>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Width</label>
                                        <input type="number" wire:model="pieces.{{ $index }}.width"
                                            class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="4" step="0.01" required>
                                    </div>
                                    <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                        <span class="text-xl">×</span>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Height</label>
                                        <input type="number" wire:model="pieces.{{ $index }}.height"
                                            class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="2.5" step="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Package Weight -->
                            <div class="mb-8">
                                <h6 class="text-base font-medium text-gray-800 dark:text-gray-200 mb-4">Package Weight
                                </h6>
                                <div class="grid grid-cols-3 gap-4 items-end max-w-md">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pounds</label>
                                        <input type="number" wire:model="pieces.{{ $index }}.weight"
                                            class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="1.4" step="0.01" required>
                                    </div>
                                    <div
                                        class="flex items-center justify-center text-gray-500 dark:text-gray-400 pb-3">
                                        <span class="text-xl">+</span>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ounces</label>
                                        <input type="number"
                                            class="w-full bg-gray-100 dark:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-500 dark:text-gray-400 placeholder-gray-400 dark:placeholder-gray-400"
                                            placeholder="0" step="0.01" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance & Value -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance
                                        Amount ($)</label>
                                    <input type="number" wire:model="pieces.{{ $index }}.insuranceAmount"
                                        class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="12.15" step="0.01" required>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Declared
                                        Value ($)</label>
                                    <input type="number" wire:model="pieces.{{ $index }}.declaredValue"
                                        class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="1.00" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <x-button type="submit" wire:loading.attr="disabled" class="px-8 py-3">
                        <span wire:loading.remove>Get Shipping Quotes</span>
                        <span wire:loading>Getting Quotes...</span>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Display Quotes Results -->
        @if ($hasResponse)
            <x-card class="mt-6">
                <x-slot:header>
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold">Choose a Service</h3>
                            @if (!empty($quotes))
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Found {{ count($quotes) }} shipping option(s)
                                </p>
                            @endif
                        </div>
                    </div>
                </x-slot:header>

                @if (!empty($errorMessage))
                    <x-alert text="{{ $errorMessage }}" color="red" />
                @elseif(!empty($quotes))
                    <div class="space-y-1">
                        @foreach ($quotes as $index => $quote)
                            @php
                                $carrierColor = match (strtolower($quote['carrierCode'] ?? '')) {
                                    'fedex' => 'border-l-purple-500',
                                    'ups' => 'border-l-yellow-600',
                                    'usps' => 'border-l-blue-600',
                                    'dhl' => 'border-l-red-600',
                                    default => 'border-l-gray-400',
                                };

                                $isBest = $index === 0;
                                $isCheapest =
                                    collect($quotes)->pluck('totalAmount')->min() == ($quote['totalAmount'] ?? 0);
                            @endphp

                            <div
                                class="border rounded-lg hover:shadow-md transition-shadow cursor-pointer {{ $carrierColor }} border-l-4">
                                <div
                                    class="p-4 {{ $index === 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-white dark:bg-gray-800' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start space-x-3">
                                            <!-- Carrier Logo/Icon -->
                                            <div class="flex-shrink-0 mt-1">
                                                @if (strtolower($quote['carrierCode'] ?? '') === 'fedex')
                                                    <div
                                                        class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center">
                                       
                                                        <x-avatar image="{{ asset('assets/images/fedex.svg') }}" md />
                                                    </div>
                                                @elseif(strtolower($quote['carrierCode'] ?? '') === 'ups')
                                                    <div
                                                        class="w-8 h-8 bg-yellow-600 rounded flex items-center justify-center">
                                                        <x-avatar image="{{ asset('assets/images/ups.svg') }}" md />

                                                    </div>
                                                @elseif(strtolower($quote['carrierCode'] ?? '') === 'usps')
                                                    <div
                                                        class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                                                        <x-avatar image="{{ asset('assets/images/usps.svg') }}" md />

                                                    </div>
                                                @elseif(strtolower($quote['carrierCode'] ?? '') === 'dhl')
                                                    <div
                                                        class="w-8 h-8 bg-red-600 rounded flex items-center justify-center">
                                                        <x-avatar image="{{ asset('assets/images/dhl.svg') }}" md />

                                                    </div>
                                                @else
                                                    <div
                                                        class="w-8 h-8 bg-gray-500 rounded flex items-center justify-center">
                                                        <span
                                                            class="text-white text-xs font-bold">{{ strtoupper(substr($quote['carrierCode'] ?? 'N/A', 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white">
                                                        {{ $quote['serviceDescription'] ?? 'N/A' }}
                                                    </h4>
                                                    @if ($isBest)
                                                        <span
                                                            class="bg-gray-800 text-white text-xs px-2 py-1 rounded font-medium">BEST</span>
                                                    @endif
                                                    @if ($isCheapest)
                                                        <span
                                                            class="bg-green-600 text-white text-xs px-2 py-1 rounded font-medium">CHEAPEST</span>
                                                    @endif
                                                </div>

                                                <div class="text-sm text-blue-600 dark:text-blue-400 mb-2">
                                                    ${{ number_format($quote['insuranceAmount'] ?? 0, 0) }} carrier
                                                    liability
                                                    @if (isset($quote['estimatedDelivery']))
                                                        • {{ $quote['estimatedDelivery'] }}
                                                    @else
                                                        • Estimated delivery in 3-5 business days
                                                    @endif
                                                </div>

                                                @php
                                                    $totalSurcharges = collect($quote['surcharges'] ?? [])->sum(
                                                        'amount',
                                                    );
                                                    $baseAmount = $quote['baseAmount'] ?? 0;
                                                    $totalAmount = $quote['totalAmount'] ?? 0;
                                                    $savingsPercent =
                                                        $baseAmount > 0
                                                            ? round(
                                                                (($baseAmount + $totalSurcharges - $totalAmount) /
                                                                    ($baseAmount + $totalSurcharges)) *
                                                                    100,
                                                            )
                                                            : 0;
                                                @endphp

                                                @if ($savingsPercent > 0)
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                                        Save {{ $savingsPercent }}% • Deepest discount available
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            @if ($baseAmount > $totalAmount)
                                                <div class="text-sm text-gray-500 line-through">
                                                    ${{ number_format($baseAmount + $totalSurcharges, 2) }} retail
                                                </div>
                                            @endif
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                ${{ number_format($totalAmount, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    @if (isset($quote['surcharges']) && !empty($quote['surcharges']))
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                            <details class="group">
                                                <summary
                                                    class="text-sm text-blue-600 dark:text-blue-400 cursor-pointer hover:text-blue-800 dark:hover:text-blue-300">
                                                    View breakdown
                                                </summary>
                                                <div class="mt-2 text-sm space-y-1">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Base
                                                            rate:</span>
                                                        <span
                                                            class="text-gray-900 dark:text-white">${{ number_format($baseAmount, 2) }}</span>
                                                    </div>
                                                    @foreach ($quote['surcharges'] as $surcharge)
                                                        <div class="flex justify-between">
                                                            <span
                                                                class="text-gray-600 dark:text-gray-400">{{ $surcharge['description'] ?? 'Additional charge' }}:</span>
                                                            <span
                                                                class="text-gray-900 dark:text-white">${{ number_format($surcharge['amount'] ?? 0, 2) }}</span>
                                                        </div>
                                                    @endforeach
                                                    <div
                                                        class="flex justify-between font-medium pt-1 border-t border-gray-200 dark:border-gray-600">
                                                        <span class="text-gray-900 dark:text-white">Total:</span>
                                                        <span
                                                            class="text-gray-900 dark:text-white">${{ number_format($totalAmount, 2) }}</span>
                                                    </div>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">📦</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No shipping quotes available
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Please check your shipping details and try
                            again.</p>
                    </div>
                @endif
            </x-card>
        @endif
    </div>
