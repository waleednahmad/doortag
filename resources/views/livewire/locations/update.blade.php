<div>
    <x-modal :title="__('Update Location: #:id', ['id' => $location?->id])" wire>
        <form id="location-update-{{ $location?->id }}" wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input label="{{ __('Company Name') }} *" wire:model="location.company_name" required />
                </div>

                <div>
                    <x-input label="{{ __('Contact Name') }} *" wire:model="location.name" required />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input label="{{ __('Email') }} *" wire:model="location.email" type="email" required />
                </div>

                <div>
                    <x-input label="{{ __('Phone') }} *" wire:model="location.phone" required />
                </div>
            </div>

            <div>
                <x-input label="{{ __('Address') }} *" wire:model="location.address" required />
            </div>

            <div>
                <x-input label="{{ __('Address 2') }}" wire:model="location.address2" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input label="{{ __('City') }} *" wire:model="location.city" required />
                </div>

                <div>
                    <x-input label="{{ __('State') }} *" wire:model="location.state" required />
                </div>

                <div>
                    <x-input label="{{ __('Zipcode') }} *" wire:model="location.zipcode" required />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input label="{{ __('Tax ID') }} *" wire:model="location.tax_id" required />
                </div>

                <div>
                    <x-input label="{{ __('Years in Business') }} *" wire:model="location.years_in_business"
                        type="number" min="0" required />
                </div>
            </div>

            <div>
                <x-select.native label="{{ __('Business Type') }} *" wire:model="location.business_type" required>
                    <option value="retail">{{ __('Retail') }}</option>
                    <option value="wholesale">{{ __('Wholesale') }}</option>
                </x-select.native>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input label="{{ __('Margin %') }} *" wire:model="location.margin" type="number" step="0.01"
                        min="0" required />
                </div>

                <div>
                    <x-input label="{{ __('Customer Margin %') }} *" wire:model="location.customer_margin"
                        type="number" step="0.01" min="0" required />
                </div>

                <div>
                    <x-input label="{{ __('Tax %') }} *" wire:model="location.tax_percentage" type="number"
                        step="0.01" min="0" required />
                </div>
            </div>
{{-- 
            <div>
                <x-input label="{{ __('Stripe Customer ID') }}" wire:model="location.stripe_customer_id" />
            </div> --}}

            <div class="grid grid-cols-3 gap-4">
                {{-- <div>
                    <x-input label="{{ __('Carrier ID') }}" wire:model="location.carrier_id" />
                </div> --}}

                <div>
                    <x-toggle label="{{ __('Status') }} *" wire:model="location.status" />
                </div>

                <div>
                    <x-toggle label="{{ __('Residential Address') }} *"
                        wire:model="location.address_residential_indicator" />
                </div>

            </div>

            <div>
                <x-textarea label="{{ __('Notes') }}" wire:model="location.notes" rows="3" />
            </div>
        </form>
        <x-slot:footer>
            <x-button type="submit" form="location-update-{{ $location?->id }}" loading="save">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
