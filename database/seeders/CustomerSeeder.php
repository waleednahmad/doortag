<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = Customer::where('email', 'apnabazartampa@hotmail.com')->first();
        if (!$customer) {
            Customer::create([
                'name' => 'Apna Bazaar International',
                'email' => 'apnabazartampa@hotmail.com',
                'phone' => '(813) 903-1774',
                'address' => '1730 E Fowler Ave',
                'address2' => '',
                'city' => 'Tampa',
                'state' => 'FL',
                'zipcode' => '33612',
                'margin' => 20.00,
                'password' => Hash::make('Hello@2025'),
            ]);
        }

        $customer2 = Customer::where('email', 'info@doortag.net')->first();
        if (!$customer2) {
            Customer::create([
                'name' => 'DoorTag Shipper',
                'email' => 'info@doortag.net',
                'phone' => '(813) 903-1774',
                'address' => '1730 E Fowler Ave',
                'address2' => '',
                'city' => 'Tampa',
                'state' => 'FL',
                'zipcode' => '33612',
                'margin' => 20.00,
                'password' => Hash::make('Hello@2025'),
            ]);
        }
    }
}
