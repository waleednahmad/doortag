<div>
    <x-button :text="__('Create New Customer')" wire:click="$toggle('modal')" sm />

    <x-modal :title="__('Create New Customer')" wire x-on:open="setTimeout(() => $refs.name.focus(), 250)">
        <form id="customer-create" wire:submit="save" class="space-y-4">
            <div>
                <x-select.native label="{{ __('Location') }} *" wire:model="customer.location_id" required>
                    <option value="">{{ __('Select Location') }}</option>
                    @foreach(\App\Models\Location::all() as $location)
                        <option value="{{ $location->id }}">{{ $location->company_name }}</option>
                    @endforeach
                </x-select.native>
            </div>

            <div>
                <x-input label="{{ __('Name') }} *" x-ref="name" wire:model="customer.name" required />
            </div>

            <div>
                <x-input label="{{ __('Email') }} *" wire:model="customer.email" type="email" required />
            </div>

            <div>
                <x-input label="{{ __('Phone') }} *" wire:model="customer.phone" required />
            </div>

            <div>
                <x-password label="{{ __('Password') }} *"
                            wire:model="password"
                            rules
                            generator
                            x-on:generate="$wire.set('password_confirmation', $event.detail.password)"
                            required />
            </div>

            <div>
                <x-password label="{{ __('Confirm Password') }} *" wire:model="password_confirmation" rules required />
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
            <x-button type="submit" form="customer-create">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
