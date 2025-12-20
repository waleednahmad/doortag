<?php

namespace App\Livewire\Auth;

use App\Models\Location;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Register extends Component
{
    use Interactions;

    // Location fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $company_name = '';
    public $address = '';
    public $address2 = '';
    public $city = '';
    public $state = '';
    public $zipcode = '';
    public $tax_id = '';
    public $years_in_business = '';
    public $business_type = 'retail';
    public $notes = '';

    // Password fields
    public $password = '';
    public $password_confirmation = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email|unique:users,email',
            'phone' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'years_in_business' => 'nullable|integer|min:0',
            'business_type' => 'required|in:retail,wholesale',
            'notes' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Create Location
            $location = Location::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company_name' => $this->company_name,
                'address' => $this->address,
                'address2' => $this->address2,
                'city' => $this->city,
                'state' => $this->state,
                'zipcode' => $this->zipcode,
                'tax_id' => $this->tax_id,
                'years_in_business' => $this->years_in_business,
                'business_type' => $this->business_type,
                'notes' => $this->notes,
                'status' => false,
            ]);

            // Create Customer associated with the Location
            Customer::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'location_id' => $location->id,
                'is_admin' => true, // Default admin for this location
            ]);


            $location->stripePaymentMethods()->createMany([
                [
                    'payment_method_name' => "Customer Card", //stripe_customer_id
                    'payment_method_id' => null,
                    'is_default' => false,
                    'is_active' =>  false,
                ],
                [
                    'payment_method_name' => "Terminal Reader", // stripe_terminal_id
                    'payment_method_id' =>   null,
                    'is_default' => false,
                    'is_active' => false,
                ],
            ]);

            DB::commit();

            $this->toast()->success('Registration Successful!', 'Your account has been created. You can now login.')->send();

            return redirect()->route('login')->with('registeration-success', true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Registration Failed', $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
