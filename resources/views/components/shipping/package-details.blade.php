@props([
    'package' => [],
    'selectedPackage' => null,
    'isInsuranceChecked' => false,
])

<section class="mt-3">
    <div class="flex items-center justify-between mb-3 sm:mb-4">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
            Package Details
        </h2>
    </div>

    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 border border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-medium text-sm sm:text-base text-gray-800 dark:text-gray-200">
                Package 1
            </h5>
        </div>

        <!-- Package Dimensions -->
        @if ($selectedPackage && $selectedPackage['package_code'] == 'custom')
            <div class="mb-6 sm:mb-8">
                <h6 class="text-sm sm:text-base font-medium text-gray-800 dark:text-gray-200 mb-3 sm:mb-4">
                    Package Dimensions (Inches)
                </h6>

                <!-- Desktop Layout (Large screens) -->
                <div class="hidden lg:grid lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <x-number label="Length" step="0.1" min="0" wire:model="package.length" />
                    </div>
                    <div>
                        <x-number label="Width" step="0.1" min="0" wire:model="package.width" />
                    </div>
                    <div>
                        <x-number label="Height" step="0.1" min="0" wire:model="package.height" />
                    </div>
                    <div>
                        <x-select.styled label="Unit" wire:model="package.dimension_unit"
                            :options="[['label' => 'Inch', 'value' => 'inch'], ['label' => 'Cm', 'value' => 'cm']]" />
                    </div>
                </div>

                <!-- Tablet Layout (Medium screens) -->
                <div class="hidden md:grid lg:hidden md:grid-cols-3 gap-4">
                    <div>
                        <x-number label="Length" step="0.1" min="0" wire:model="package.length" />
                    </div>
                    <div>
                        <x-number label="Width" step="0.1" min="0" wire:model="package.width" />
                    </div>
                    <div>
                        <x-number label="Height" step="0.1" min="0" wire:model="package.height" />
                    </div>
                </div>

                <!-- Mobile Layout (Small screens) -->
                <div class="md:hidden space-y-3">
                    <div class="grid grid-cols-1 gap-3">
                        <x-number label="Length" step="0.1" min="0" wire:model="package.length" />
                        <x-number label="Width" step="0.1" min="0" wire:model="package.width" />
                        <x-number label="Height" step="0.1" min="0" wire:model="package.height" />
                        <x-select.styled label="Unit" wire:model="package.dimension_unit"
                            :options="[['label' => 'Inch', 'value' => 'inch'], ['label' => 'Cm', 'value' => 'cm']]" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Weight -->
        <div class="mb-6 sm:mb-8">
            <h6 class="text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-3 sm:mb-4">
                Package Weight
            </h6>
            <div class="max-w-md">
                <x-number label="Weight (Pounds) *" step="0.1" min="0.1" wire:model="package.weight" required />
            </div>
        </div>
    </div>

    <!-- Insurance Section -->
    <div class="my-4 flex flex-col gap-3" x-data="{ insuranceChecked: @entangle('isInsuranceChecked') }">
        <x-checkbox label="Insurance" wire:model.live='isInsuranceChecked'
            hint="Enter the total value of your shipment to add coverage by InsureShield" class="text-sm" />

        <div x-show="insuranceChecked" x-transition>
            <x-number label="Declared Package Value ($) *" placeholder="Enter package value"
                :required="$isInsuranceChecked" step="0.01" wire:model='package.insured_value' min="100" />
        </div>
    </div>
</section>
