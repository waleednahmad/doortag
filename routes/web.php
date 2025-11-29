<?php

use App\Livewire\Shipments\Index as ShipmentsIndex;
use App\Livewire\Shipping\EstimateRates\Index as EstimateRatesIndex;
use App\Livewire\Shipping\Index as ShippingIndex;
use App\Livewire\Shipping\Fedex\Index as FedexShippingIndex;
use App\Livewire\Shipping\ShipEngine\Index as ShipEngineShippingIndex;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Users\Index;

// Route::view('/', 'welcome')->name('welcome');
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/test-email', function () {
    try {
        // Get the latest shipment to test with real data
        $shipment = \App\Models\Shipment::latest()->first();
        
        if (!$shipment) {
            return 'No shipments found in the database. Please create a shipment first.';
        }

        // Prepare shipment data using the same logic as ShipmentObserver
        $requestData = [];
        if ($shipment->request_data) {
            $requestData = json_decode($shipment->request_data, true) ?? [];
            $shipmentData = is_array($requestData['shipment'] ?? null) ? $requestData['shipment'] : [];
        } else {
            $shipmentData = json_decode($shipment->shipment_data, true) ?? [];
        }

        $trackingResponse = json_decode($shipment->shipment_data, true) ?? [];
        $tracking = $trackingResponse['tracking_number'] ?? '';
        $trackingUrl = $trackingResponse['tracking_url'] ?? '';

        $logoPath = public_path('assets/images/logo-black.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $shipFromAddress = is_array($shipmentData['ship_from'] ?? null) ? $shipmentData['ship_from'] : [];
        $shipToAddress = is_array($shipmentData['ship_to'] ?? null) ? $shipmentData['ship_to'] : [];
        $customs = is_array($shipmentData['customs'] ?? null) ? $shipmentData['customs'] : [];
        $packages = is_array($shipmentData['packages'] ?? null) ? $shipmentData['packages'] : [];
        $taxIdentifiers = is_array($shipmentData['tax_identifiers'] ?? null) ? $shipmentData['tax_identifiers'] : [];

        $serviceCode = $trackingResponse['service_code'] ?? '';
        $carrierCode = $trackingResponse['carrier_code'] ?? '';

        $transformedPackages = [];
        foreach ($packages as $pkg) {
            $transformedPackages[] = [
                'weight' => is_array($pkg['weight'] ?? null) ? $pkg['weight']['value'] ?? 0 : 0,
                'weight_unit' => is_array($pkg['weight'] ?? null) ? $pkg['weight']['unit'] ?? 'pound' : 'pound',
                'length' => is_array($pkg['dimensions'] ?? null) ? $pkg['dimensions']['length'] ?? null : null,
                'width' => is_array($pkg['dimensions'] ?? null) ? $pkg['dimensions']['width'] ?? null : null,
                'height' => is_array($pkg['dimensions'] ?? null) ? $pkg['dimensions']['height'] ?? null : null,
                'dimension_unit' => is_array($pkg['dimensions'] ?? null) ? $pkg['dimensions']['unit'] ?? 'inch' : 'inch',
                'insured_value' => is_array($pkg['insured_value'] ?? null) ? $pkg['insured_value']['amount'] ?? 0 : 0,
                'package_code' => $pkg['package_code'] ?? 'package',
                'package_name' => ucfirst(str_replace('_', ' ', $pkg['package_code'] ?? 'Package')),
            ];
        }

        $signatureBase64 = '';
        if ($shipment->signature_path && file_exists(public_path($shipment->signature_path))) {
            $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($shipment->signature_path)));
        }

        $emailData = [
            'shipFromAddress' => $shipFromAddress,
            'shipToAddress' => $shipToAddress,
            'packages' => $transformedPackages,
            'customs' => $customs,
            'tax_identifiers' => $taxIdentifiers,
            'shipDate' => $shipmentData['ship_date'] ?? null,
            'serviceCode' => $serviceCode,
            'carrierCode' => $carrierCode,
            'selectedRate' => [
                'service_type' => $serviceCode,
                'carrier_code' => $carrierCode,
                'estimated_delivery_date' => $trackingResponse['estimated_delivery_date'] ?? null,
                'calculated_amount' => $shipment->end_user_total ?? $shipment->customer_total ?? $shipment->origin_total ?? 0,
            ],
            'end_user_total' => $shipment->end_user_total,
            'customer_total' => $shipment->customer_total,
            'origin_total' => $shipment->origin_total,
            'total_weight' => $shipment->total_weight ?? 0,
            'logoBase64' => $logoBase64,
            'trackingNumber' => $tracking,
            'trackingUrl' => $trackingUrl,
            'signatureBase64' => $signatureBase64,
            'ship_to_address_country_full_name' => $requestData['ship_to_address_country_full_name'] ?? ($shipToAddress['country_code'] ?? ''),
            'orderNumber' => $shipment->id,
            'paymentNumber' => $shipment->stripe_payment_intent_id,
            'stripe_amount_paid' => $shipment->stripe_amount_paid,
            'packaging_amount' => $shipment->packaging_amount,
            'carrier_delivery_days' => $shipment->carrier_delivery_days,
            'estimated_delivery_date' => $shipment->estimated_delivery_date,
        ];

        // Send the email using ShipmentReceiptMail
        \Illuminate\Support\Facades\Mail::to('waleed.n.ahmad@gmail.com')
            ->send(new \App\Mail\ShipmentReceiptMail($emailData));

        return 'Test shipment receipt email sent successfully to waleed.n.ahmad@gmail.com (Shipment ID: ' . $shipment->id . ')';
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
})->name('test.email');

Route::middleware(['auth:web,customer'])->group(function () {
    // Route::view('/dashboard', 'dashboard')->name('dashboard');
    // Route::get('/dashboard', ShippingIndex::class)->name('dashboard');
    // Route::get('/shipping/fedex', FedexShippingIndex::class)->name('shipping.fedex.index');
    Route::get(
        '/dashboard',
        function () {
            return redirect()->route('shipping.shipengine.index');
        }
    )->name('dashboard');

    Route::get('/users', Index::class)->name('users.index');

    Route::get('/user/profile', Profile::class)->name('user.profile');
    Route::get('/shipping/test', ShippingIndex::class)->name('shipping.index');
    Route::get('/shipping/fedex', function () {
        return redirect()->route('shipping.shipengine.index');
    })->name('shipping.fedex.index');
    Route::get('/shipping', ShipEngineShippingIndex::class)->name('shipping.shipengine.index');
    Route::get('/shipments', ShipmentsIndex::class)->name('shipments.index');
    Route::get('/shipments/receipt/{labelId}', [App\Http\Controllers\ShipmentController::class, 'showReceipt'])->name('shipments.receipt');
    Route::get('/rates', EstimateRatesIndex::class)->name('rates.index');
});

require __DIR__ . '/auth.php';
