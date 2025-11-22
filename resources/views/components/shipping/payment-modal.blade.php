@props([
    'showPaymentModal' => false,
    'paymentProcessing' => false,
    'paymentError' => null,
    'paymentSuccessful' => false,
    'availableReaders' => [],
    'selectedReaderId' => null,
    'selectedRate' => null,
    'end_user_total' => 0,
])

<x-modal wire="showPaymentModal" size="4xl" persistent>
    <x-slot:title>
        💳 Payment for Shipping Label
    </x-slot:title>
    <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">
                Payment Summary
            </h3>
            @if ($selectedRate)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">Shipping Cost:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($selectedRate['shipping_amount']['amount'] ?? 0, 2) }}
                    </span>
                </div>
                <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-200 dark:border-blue-700">
                    <span class="font-semibold text-gray-900 dark:text-white">Total Amount:</span>
                    <span class="font-bold text-lg text-blue-600 dark:text-blue-400">
                        ${{ number_format($end_user_total, 2) }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Reader Selection -->
        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                Select Card Reader
            </h4>
            @if (count($availableReaders) > 0)
                <div class="space-y-2">
                    @foreach ($availableReaders as $reader)
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <input type="radio" wire:model="selectedReaderId" value="{{ $reader['id'] }}" class="mr-3" />
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $reader['label'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $reader['device_type'] ?? 'Card Reader' }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        No card readers available. Please make sure your reader is connected.
                    </p>
                </div>
            @endif
        </div>

        <!-- Payment Status -->
        @if ($paymentProcessing)
            <div class="p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg text-center">
                <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                    Processing Payment
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Please present your card to the reader and follow the prompts...
                </p>
            </div>
        @endif

        @if ($paymentError)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-semibold text-red-800 dark:text-red-200 mb-1">
                            Payment Failed
                        </h3>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            {{ $paymentError }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($paymentSuccessful)
            <div class="p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-center">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-4xl"></i>
                </div>
                <h3 class="font-semibold text-green-900 dark:text-green-100 mb-2">
                    Payment Successful!
                </h3>
                <p class="text-sm text-green-700 dark:text-green-300">
                    Your shipping label is being created...
                </p>
            </div>
        @endif
    </div>

    <div class="mt-6 flex justify-between">
        <x-button wire:click="$set('showPaymentModal', false)" color="gray" :disabled="$paymentProcessing">
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
