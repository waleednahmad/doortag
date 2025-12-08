<?php

namespace App\Observers;

use App\Mail\ShipmentReceiptMail;
use App\Models\Shipment;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ShipmentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Shipment "created" event.
     */
    public function created(Shipment $shipment): void
    {
        // Check if there's a sender email in the shipment data
        $shipFromAddress = is_array($shipment->ship_from_address)
            ? $shipment->ship_from_address
            : json_decode($shipment->ship_from_address, true);

        $senderEmail = $shipFromAddress['email'] ?? null;

        // Only send email if sender email exists
        if (!empty($senderEmail)) {
            try {
                // Prepare shipment data for the email template
                $shipmentData = $this->prepareShipmentData($shipment);

                // Send email using Mailgun to the sender
                Mail::to($senderEmail)->send(new ShipmentReceiptMail($shipmentData));
            } catch (\Exception $e) {
                // Log the error but don't fail the shipment creation
                Log::error('Failed to send shipment receipt email', [
                    'shipment_id' => $shipment->id,
                    'email' => $senderEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Prepare shipment data for the email template - matches ShipmentController showReceipt exactly
     */
    private function prepareShipmentData(Shipment $shipment): array
    {
        try {
            // Prefer request_data (which contains all original data) over response data
            $requestData = [];
            if ($shipment->request_data) {
                $requestData = json_decode($shipment->request_data, true) ?? [];
                // Extract from nested shipment structure
                $shipmentData = is_array($requestData['shipment'] ?? null) ? $requestData['shipment'] : [];
            } else {
                // Fallback to response data for older records
                $shipmentData = json_decode($shipment->shipment_data, true) ?? [];
            }

            // Get tracking number from response data (shipment_data from API response)
            $trackingResponse = json_decode($shipment->shipment_data, true) ?? [];
            $tracking = $trackingResponse['tracking_number'] ?? '';
            $trackingUrl = $trackingResponse['tracking_url'] ?? '';

            // Prepare logo
            $logoPath = public_path('assets/images/logo-black.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            // Extract shipment details from request data (which has all the data we sent)
            $shipFromAddress = is_array($shipmentData['ship_from'] ?? null) ? $shipmentData['ship_from'] : [];
            $shipToAddress = is_array($shipmentData['ship_to'] ?? null) ? $shipmentData['ship_to'] : [];
            $customs = is_array($shipmentData['customs'] ?? null) ? $shipmentData['customs'] : [];
            $packages = is_array($shipmentData['packages'] ?? null) ? $shipmentData['packages'] : [];

            // Get tax identifiers from request data
            $taxIdentifiers = is_array($shipmentData['tax_identifiers'] ?? null) ? $shipmentData['tax_identifiers'] : [];

            // Get service details from response data (shipment_data)
            $serviceCode = $trackingResponse['service_code'] ?? '';
            $carrierCode = $trackingResponse['carrier_code'] ?? '';

            // Transform packages for blade view
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

            // Prepare signature
            $signatureBase64 = '';
            if ($shipment->signature_path && file_exists(public_path($shipment->signature_path))) {
                $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($shipment->signature_path)));
            }

            return [
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
                'tax_amount' => $shipment->tax_amount,
                'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            Log::error('Error preparing shipment data for email', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
