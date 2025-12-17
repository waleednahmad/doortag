<div>
    <x-modal :title="__('Manage Payment Methods: :name', ['name' => $location?->name])" wire size="2xl">
        <form id="payment-methods-form-{{ $location?->id }}" wire:submit="save" class="space-y-6">
            @if($location && count($paymentMethods) > 0)
                @foreach($paymentMethods as $index => $method)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                        <div class="space-y-4">
                            <!-- Payment Method Name (Read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Payment Method Name') }}
                                </label>
                                <div class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-500 dark:text-gray-400">
                                    {{ $method['payment_method_name'] }}
                                </div>
                            </div>

                            <!-- Payment Method ID (Editable) -->
                            <div>
                                <x-input 
                                    label="{{ __('Payment Method ID') }}" 
                                    wire:model="paymentMethods.{{ $index }}.payment_method_id" 
                                />
                            </div>

                            <!-- Toggles -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-toggle 
                                        label="{{ __('Is Default') }}" 
                                        wire:model.live="paymentMethods.{{ $index }}.is_default"
                                        hint="Only one payment method can be default" 
                                    />
                                </div>

                                <div>
                                    <x-toggle 
                                        label="{{ __('Is Active') }}" 
                                        wire:model="paymentMethods.{{ $index }}.is_active" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    {{ __('No payment methods found for this location.') }}
                </div>
            @endif
        </form>
        <x-slot:footer>
            <div class="flex justify-between items-center w-full">
                <x-button wire:click="$set('modal', false)" color="secondary">
                    @lang('Cancel')
                </x-button>
                <x-button type="submit" form="payment-methods-form-{{ $location?->id }}" loading="save">
                    @lang('Save Changes')
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
