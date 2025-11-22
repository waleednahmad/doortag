@props([
    'rates' => [],
    'sortBy' => 'price',
    'sortDirection' => 'asc',
])

@if (!empty($rates))
    <!-- Enhanced Sorting Section -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Sort Label -->
            <div class="flex items-center">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-sort mr-2 text-gray-500 dark:text-gray-400"></i>
                    <span class="font-medium">Sort by:</span>
                </div>
            </div>

            <!-- Sort Buttons -->
            <div class="flex items-center justify-between gap-1 bg-white dark:bg-gray-700 rounded-lg p-1 shadow-sm border border-gray-200 dark:border-gray-600">
                <button wire:click="sortByPrice"
                    class="group relative inline-flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-all duration-200 ease-in-out
                    {{ $sortBy === 'price' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        <span>Price</span>
                        @if ($sortBy === 'price')
                            <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2 text-xs"></i>
                        @endif
                    </div>
                </button>

                <button wire:click="sortByDelivery"
                    class="group relative inline-flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-all duration-200 ease-in-out
                    {{ $sortBy === 'delivery' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Delivery</span>
                        @if ($sortBy === 'delivery')
                            <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2 text-xs"></i>
                        @endif
                    </div>
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
                    <i class="fas fa-{{ $sortBy === 'price' ? 'dollar-sign' : 'clock' }} mr-2 text-xs"></i>
                    <span>
                        {{ $sortBy === 'price' ? 'Sorted by price' : 'Sorted by delivery' }}
                        ({{ $sortDirection === 'asc' ? 'lowest to highest' : 'highest to lowest' }})
                    </span>
                    <div class="ml-2 flex items-center">
                        <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} text-xs"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
