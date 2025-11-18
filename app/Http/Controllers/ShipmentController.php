<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShipmentController
{
    public function showReceipt($labelId)
    {
        try {
            $authUser = Auth::user();
            $shipment = $authUser->shipments()->where('label_id', $labelId)->first();

            if (!$shipment) {
                abort(404, 'Shipment not found.');
            }

            // Prefer request_data (which contains all original data) over response data
            if ($shipment->request_data) {
                $requestData = json_decode($shipment->request_data, true);
                // Extract from nested shipment structure
                $shipmentData = is_array($requestData['shipment'] ?? null) ? $requestData['shipment'] : [];
            } else {
                // Fallback to response data for older records
                $shipmentData = json_decode($shipment->shipment_data, true);
            }

            if (!is_array($shipmentData)) {
                Log::error('Invalid shipment_data: ' . ($shipment->shipment_data ?? $shipment->request_data));
                abort(500, 'Invalid shipment data.');
            }

            // Get tracking number from response data (shipment_data from API response)
            $trackingResponse = json_decode($shipment->shipment_data, true);
            $tracking = $trackingResponse['tracking_number'] ?? '';

            $logoPath = public_path('assets/images/logo-black.png');
            $logoBase64 = '';

            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            // $signatureBase64 = '';
            // if ($shipment->signature_path && file_exists($shipment->signature_path)) {

            //     $signaturePath = $shipment->signature_path;
            //     $fullSignaturePath = $signaturePath;

            //     // Handle different path formats - signature_path stored as "storage/signatures/2025/11/16/uuid.png"
            //     // Files are actually in storage/app/public/signatures/
            //     if (strpos($signaturePath, 'storage/') === 0) {
            //         // Remove 'storage/' prefix and look in storage/app/public
            //         $cleanPath = str_replace('storage/', '', $signaturePath);
            //         $fullSignaturePath = storage_path('app/public/' . $cleanPath);
            //     } elseif (strpos($signaturePath, 'signatures/') === 0) {
            //         // Path is relative to storage/app/public
            //         $fullSignaturePath = storage_path('app/public/' . $signaturePath);
            //     } else {
            //         // Assume it's relative to storage/app/public
            //         $fullSignaturePath = storage_path('app/public/' . $signaturePath);
            //     }

            //     if (file_exists($fullSignaturePath)) {
            //         $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($fullSignaturePath));
            //     } else {
            //         Log::warning('Signature file not found: ' . $fullSignaturePath . ' (original: ' . $signaturePath . ')');
            //     }
            // }else {
            //     $blankSignaturePath = public_path('assets/images/signature-blank.png');
            //     if (file_exists($blankSignaturePath)) {
            //         $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($blankSignaturePath));
            //     }
            // }

            // Extract shipment details from request data (which has all the data we sent)
            $shipFromAddress = is_array($shipmentData['ship_from'] ?? null) ? $shipmentData['ship_from'] : [];
            $shipToAddress = is_array($shipmentData['ship_to'] ?? null) ? $shipmentData['ship_to'] : [];
            $customs = is_array($shipmentData['customs'] ?? null) ? $shipmentData['customs'] : [];
            $packages = is_array($shipmentData['packages'] ?? null) ? $shipmentData['packages'] : [];

            // Get the first package or create empty one
            $firstPackage = is_array($packages[0] ?? null) ? $packages[0] : [];

            // Get tax identifiers from request data
            $taxIdentifiers = is_array($shipmentData['tax_identifiers'] ?? null) ? $shipmentData['tax_identifiers'] : [];

            // Get service details from response data (shipment_data)
            $serviceCode = $trackingResponse['service_code'] ?? '';
            $carrierCode = $trackingResponse['carrier_code'] ?? '';

            $data = [
                'shipFromAddress' => $shipFromAddress,
                'shipToAddress' => $shipToAddress,
                'package' => [
                    'weight' => is_array($firstPackage['weight'] ?? null) ? $firstPackage['weight']['value'] ?? 0 : 0,
                    'weight_unit' => is_array($firstPackage['weight'] ?? null) ? $firstPackage['weight']['unit'] ?? 'pound' : 'pound',
                    'length' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['length'] ?? null : null,
                    'width' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['width'] ?? null : null,
                    'height' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['height'] ?? null : null,
                    'dimension_unit' => is_array($firstPackage['dimensions'] ?? null) ? $firstPackage['dimensions']['unit'] ?? 'inch' : 'inch',
                    'insured_value' => is_array($firstPackage['insured_value'] ?? null) ? $firstPackage['insured_value']['amount'] ?? 0 : 0,
                ],
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
                'carrierPackaging' => [
                    [
                        'package_code' => $firstPackage['package_code'] ?? 'package',
                        'name' => ucfirst(str_replace('_', ' ', $firstPackage['package_code'] ?? 'Package'))
                    ]
                ],
                'selectedPackaging' => $firstPackage['package_code'] ?? 'package',
                'selectedPackage' => [
                    'package_code' => $firstPackage['package_code'] ?? 'package',
                    'name' => ucfirst(str_replace('_', ' ', $firstPackage['package_code'] ?? 'Package'))
                ],
                'isInsuranceChecked' => (is_array($firstPackage['insured_value'] ?? null) ? $firstPackage['insured_value']['amount'] ?? 0 : 0) > 0,
                'end_user_total' => $shipment->end_user_total,
                'customer_total' => $shipment->customer_total,
                'origin_total' => $shipment->origin_total,
                'logoBase64' => $logoBase64,
                'trackingNumber' => $tracking,
                'signatureBase64' => ($shipment->signature_path && file_exists($shipment->signature_path)) ? asset($shipment->signature_path) : asset('assets/images/signature-blank.png'),
                'ship_to_address_country_full_name' => $requestData['ship_to_address_country_full_name'] ?? ($shipToAddress['country_code'] ?? ''),
                'orderNumber' => $shipment->id,
                'paymentNumber' => $shipment->stripe_payment_intent_id,
                'stripe_amount_paid' => $shipment->stripe_amount_paid,
                'packaging_amount' => $shipment->packaging_amount,
            ];

            return view('pdfs.shipment-details', $data);
        } catch (\Exception $e) {
            Log::error('Receipt Display Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Failed to display receipt: ' . $e->getMessage());
        }
    }
}
