<form wire:submit.prevent="login" class="space-y-6" id="login-form">
    <h2 class="text-3xl font-bold text-center mb-8">Ahoy, Captain!</h2>
    <div class="space-y-4">
        <x-input label="Email *" type="email" name="email" :value="old('email', 'test@example.com')" required autofocus wire:model="email"
            autocomplete="username" icon="envelope-open" />

        <x-password label="Password *" type="password" name="password" required autocomplete="current-password"
            wire:model.defer="password" icon="lock-closed" />

        {{-- <x-input label="Email *" type="email" name="email" :value="old('email', 'test@example.com')" required autofocus wire:model="email"
            autocomplete="username" /> --}}

        {{-- <x-password label="Password *" type="password" name="password" required autocomplete="current-password"
            wire:model.defer="password" /> --}}


    </div>
    <x-button type="submit" wire:loading.attr="disabled"
        class="w-full h-[100px] bg-[#00a9ff] hover:bg-blue-600 text-white py-2 rounded-xl text-[30px] font-bold">
        <span wire:loading.remove>
            Yarrrr, log me in
        </span>
        <span wire:loading>
            <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"
                role="status">
                <span class="sr-only">Loading...</span>
            </span>
        </span>
    </x-button>

    <div class="block mt-2">
        <x-checkbox label="Stay signed in" id="remember_me" type="checkbox" name="remember" />
    </div>
    {{-- 
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
            <span wire:loading>
                <span
                    class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"
                    role="status">
                    <span class="sr-only">Loading...</span>
                </span>
            </span>
        </x-button>
    </div> --}}
</form>
