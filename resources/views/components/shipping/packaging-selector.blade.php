@props([
    'selectedPackaging' => 'custom',
    'selectedPackage' => null,
    'carrierPackaging' => [],
    'carriers' => [],
    'selectedCarrier' => '',
])

<section class="mt-3">
    <h1 class="text-base sm:text-lg font-semibold mb-3 sm:mb-2 text-gray-800 dark:text-gray-200">
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
                    @else
                    @endif
                </span>
                <div class="ml-[.9em]">
                    <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100">
                        {{ $selectedPackage['name'] ?? 'Select Packaging' }}
                    </h1>
                    <p class="text-[0.85em] text-gray-600 dark:text-gray-400">
                        {{ $selectedPackage['package_code'] ?? 'Choose a package type' }}
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
                        <span class="w-[130px] h-[90px] flex items-center justify-center">
                            <i class="fas fa-box text-[2em] text-gray-400 dark:text-gray-500"></i>
                        </span>
                        <div class="ml-[.9em]">
                            <h1 class="text-[1em] font-[400] text-gray-900 dark:text-gray-100">
                                {{ $package['name'] }}
                            </h1>
                            <p class="text-[0.85em] text-gray-600 dark:text-gray-400">
                                {{ $package['package_code'] }}
                            </p>
                        </div>
                    </div>
                    @if ($selectedPackaging === $package['package_code'])
                        <i class="fas fa-check text-[1.5em] text-blue-600 dark:text-blue-400"></i>
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
