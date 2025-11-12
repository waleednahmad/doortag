<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'info@doortag.com')->first();
        if (!$admin) {
            User::create([
                'name' => 'DoorTag Shipper',
                'email' => 'info@doortag.com',
                'phone' => '(708) 307-7663',
                'address' => '1700 Oviedo Mall Blvd',
                'address2' => '',
                'city' => 'Oviedo',
                'state' => 'FL',
                'zipcode' => '32765',
                'password' => Hash::make('Hello@2025'),
            ]);
        }
    }
}
