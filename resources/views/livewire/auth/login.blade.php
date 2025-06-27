<form wire:submit.prevent="login" class="space-y-6" id="login-form">
    <div class="space-y-4">
        <x-input label="Email *" type="email" name="email" :value="old('email', 'test@example.com')" required autofocus wire:model="email"
            autocomplete="username" />

        <x-password label="Password *" type="password" name="password" required autocomplete="current-password"
            wire:model.defer="password" />
    </div>

    <div class="block mt-4">
        <x-checkbox label="Remember me" id="remember_me" type="checkbox" name="remember" />
    </div>

    <div class="flex items-center justify-end mt-4">
        @if (Route::has('register'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('register') }}">
                {{ __('Sign up') }}
            </a>
        @endif

        <x-button type="submit" class="ms-3" wire:loading.attr="disabled">
            <span wire:loading.remove>
                {{ __('Log in') }}
            </span>
            <span wire:loading >
                <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full" role="status">
                    <span class="sr-only">Loading...</span>
                </span>
            </span>
        </x-button>
    </div>
</form>
