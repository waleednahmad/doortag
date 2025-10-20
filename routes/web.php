<?php

use App\Livewire\Shipping\Index as ShippingIndex;
use App\Livewire\Shipping\Fedex\Index as FedexShippingIndex;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Users\Index;

// Route::view('/', 'welcome')->name('welcome');
Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

Route::middleware(['auth:web,customer'])->group(function () {
    // Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/dashboard', ShippingIndex::class)->name('dashboard');

    Route::get('/users', Index::class)->name('users.index');

    Route::get('/user/profile', Profile::class)->name('user.profile');
    Route::get('/shipping', ShippingIndex::class)->name('shipping.index');
    Route::get('/shipping/fedex', FedexShippingIndex::class)->name('shipping.fedex.index');
});

require __DIR__ . '/auth.php';
