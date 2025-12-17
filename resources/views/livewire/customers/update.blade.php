<div>
    <x-modal :title="__('Update Customer: #:id', ['id' => $customer?->id])" wire>
        <form id="customer-update-{{ $customer?->id }}" wire:submit="save" class="space-y-4">
            <div>
                <x-select.native label="{{ __('Location') }} *" wire:model="customer.location_id" required>
                    <option value="">{{ __('Select Location') }}</option>
                    @foreach(\App\Models\Location::all() as $location)
                        <option value="{{ $location->id }}">{{ $location->company_name }}</option>
                    @endforeach
                </x-select.native>
            </div>

            <div>
                <x-input label="{{ __('Name') }} *" wire:model="customer.name" required />
            </div>

            <div>
                <x-input label="{{ __('Email') }} *" wire:model="customer.email" type="email" required />
            </div>

            <div>
                <x-input label="{{ __('Phone') }} *" wire:model="customer.phone" required />
            </div>

            <div>
                <x-password label="{{ __('Password') }}"
                            hint="The password will only be updated if you set the value of this field"
                            wire:model="password"
                            rules
                            generator
                            x-on:generate="$wire.set('password_confirmation', $event.detail.password)" />
            </div>

            <div>
                <x-password label="{{ __('Confirm Password') }}" wire:model="password_confirmation" rules />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-toggle label="{{ __('Is Admin') }} *" wire:model="customer.is_admin" />
                </div>

                <div>
                    <x-toggle label="{{ __('Can modify ship from address data') }} *" wire:model="customer.can_modify_data" />
                </div>
            </div>
        </form>
        <x-slot:footer>
            <x-button type="submit" form="customer-update-{{ $customer?->id }}" loading="save">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
