<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;


class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Try to authenticate with the default 'web' guard first (User model)
        if (Auth::guard('web')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            // check if the user status == 1 or not, if not logout the user and show error message
            $user = Auth::guard('web')->user();
            if ($user && $user->location->status != 1) {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => "Your account is not active yet. Please wait while we review your application.",
                ]);

                return;
            }


            $this->redirect(route('shipping.shipengine.index'));
            return;
        }

        // If web guard fails, try customer guard
        if (Auth::guard('customer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            // check if the user status == 1 or not, if not logout the user and show error message
            $customer = Auth::guard('customer')->user();
            if ($customer && $customer->location->status != 1) {
                Auth::guard('customer')->logout();
                throw ValidationException::withMessages([
                    'email' => "Your account is not active yet. Please wait while we review your application.",
                ]);
                return;
            }

            $this->redirect(route('shipping.shipengine.index'));
            return;
        }

        // If both guards fail, increment rate limiter and throw validation error
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
