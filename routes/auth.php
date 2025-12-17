<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest:web,customer'])->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    // Route::get('register', function () {
    //     // return to login route
    //     return redirect()->route('login');
    // })->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:web,customer')->group(function () {
    Route::match(
        ['get', 'post'],
        'logout',
        [AuthenticatedSessionController::class, 'destroy']
    )->name('logout');
});
