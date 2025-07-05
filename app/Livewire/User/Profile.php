<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Alert;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]

class Profile extends Component
{
    use Alert;

    public $user;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $authenticatedUser = Auth::user();

        if ($authenticatedUser instanceof User) {
            $this->user = User::find($authenticatedUser->id);
        } elseif ($authenticatedUser instanceof Customer) {
            $this->user = Customer::find($authenticatedUser->id);
        }
    }

    public function rules(): array
    {
        return [
            'user.name' => [
                'required',
                'string',
                'max:255'
            ],
            'user.phone' => [
                'nullable',
                'string',
                'max:255'
            ],
            'user.address' => [
                'required',
                'string',
                'max:255'
            ],
            'user.address2' => [
                'nullable',
                'string',
                'max:255'
            ],
            'user.city' => [
                'required',
                'string',
                'max:255'
            ],
            'user.state' => [
                'required',
                'string',
                'max:255'
            ],
            'user.zipcode' => [
                'required',
                'string',
                'max:255'
            ],
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Rules\Password::defaults()
            ]
        ];
    }

    public function render(): View
    {
        return view('livewire.user.profile');
    }

    public function save(): void
    {
        $this->validate();

        $this->user->password = when($this->password !== null, Hash::make($this->password), $this->user->password);
        $this->user->save();

        $this->dispatch('updated', name: $this->user->name);

        $this->resetExcept('user');

        $this->success(description: 'Profile updated successfully!');
    }
}
