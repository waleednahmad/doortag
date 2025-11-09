<div @updated="$dispatch('name-updated', { name: $event.detail.name })">
    <x-card>
        <x-slot:header>
            @lang('Edit Your Profile')
        </x-slot:header>
        <form id="update-profile" wire:submit="save">
            <div class="space-y-6">
                <div>
                    <x-input label="{{ __('Name') }} *" wire:model="user.name" disabled />
                </div>
                <div>
                    <x-input label="{{ __('Email') }} *" value="{{ $user->email }}" disabled />
                </div>
                <div>
                    <x-input label="{{ __('Phone') }}" wire:model="user.phone"  disabled/>
                </div>
                <div>
                    <x-input label="{{ __('Address') }} *" wire:model="user.address" disabled />
                </div>
                <div>
                    <x-input label="{{ __('Address 2') }}" wire:model="user.address2" disabled />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input label="{{ __('City') }} *" wire:model="user.city" disabled />
                    </div>
                    <div>
                        <x-input label="{{ __('State') }} *" wire:model="user.state" disabled />
                    </div>
                    <div>
                        <x-input label="{{ __('Zip Code') }} *" wire:model="user.zipcode" disabled />
                    </div>
                </div>
                <div>
                    <x-password :label="__('Password')" :hint="__('The password will only be updated if you set the value of this field')" wire:model="password" rules generator
                        x-on:generate="$wire.set('password_confirmation', $event.detail.password)" />
                </div>
                <div>
                    <x-password :label="__('Confirm password')" wire:model="password_confirmation" rules />
                </div>
                {{-- address_residential_indicator --}}
                <div>
                    <x-checkbox label="{{ __('Residential Address') }}" wire:model="addressResidentialIndicator"
                        disabled />
                </div>

            </div>
            <x-slot:footer>
                <x-button type="submit">
                    <span wire:loading.remove>
                        Save
                    </span>
                    <span wire:loading>
                        <span
                            class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"
                            role="status">
                            <span class="sr-only">Saving...</span>
                        </span>
                    </span>
                </x-button>
            </x-slot:footer>
        </form>
        <x-slot:footer>
            <x-button type="submit" form="update-profile">
                <span wire:loading.remove>
                    Save
                </span>
                <span wire:loading>
                    <span
                        class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"
                        role="status">
                        <span class="sr-only">Saving...</span>
                    </span>
                </span>
            </x-button>
        </x-slot:footer>
    </x-card>
</div>
