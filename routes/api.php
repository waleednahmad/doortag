<?php

use App\Http\Controllers\TerminalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Stripe Terminal Routes
Route::get('/terminal/list-readers', [TerminalController::class, 'listReaders']);
Route::post('/terminal/get-reader', [TerminalController::class, 'getReader']);
Route::post('/terminal/create-location', [TerminalController::class, 'createLocation']);
Route::post('/terminal/register-reader', [TerminalController::class, 'registerReader']);
Route::post('/terminal/create-payment-intent', [TerminalController::class, 'createPaymentIntent']);
Route::post('/terminal/process-payment', [TerminalController::class, 'processPayment']);
Route::post('/terminal/simulate-payment', [TerminalController::class, 'simulatePayment']);
Route::post('/terminal/capture-payment-intent', [TerminalController::class, 'capturePaymentIntent']);
Route::post('/terminal/check-payment-status', [TerminalController::class, 'checkPaymentStatus']);
Route::post('/terminal/cancel-payment', [TerminalController::class, 'cancelPayment']);

// Shipping Payment Routes
Route::post('/terminal/shipping/create-payment-intent', [TerminalController::class, 'createShippingPaymentIntent']);
Route::post('/terminal/shipping/process-payment', [TerminalController::class, 'processShippingPayment']);
Route::post('/terminal/shipping/verify-payment', [TerminalController::class, 'verifyShippingPayment']);
Route::post('/terminal/refund-payment', [TerminalController::class, 'refundPayment'])->name('api.terminal.refund-payment');