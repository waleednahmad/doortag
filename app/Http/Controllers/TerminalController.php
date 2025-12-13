<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class TerminalController
{
    private StripeClient $stripe;

    public function __construct(bool $testMode = false)
    {
        $key = $testMode ? config('services.stripe.test_secret') : config('services.stripe.secret');
        $this->stripe = new StripeClient($key);

        Log::info('TerminalController initialized', [
            'mode' => $testMode ? 'test' : 'live',
            'key_length' => strlen($key ?? '')
        ]);
    }

    /**
     * List all Terminal Readers
     */
    public function listReaders(): JsonResponse
    {
        try {
            $readers = $this->stripe->terminal->readers->all(['limit' => 100]);
            return response()->json($readers);
        } catch (\Throwable $e) {
            Log::error('Failed to list terminal readers: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a specific Terminal Reader
     */
    public function getReader(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reader_id' => 'required|string',
            ]);

            $reader = $this->stripe->terminal->readers->retrieve($validated['reader_id']);
            return response()->json($reader);
        } catch (\Throwable $e) {
            Log::error('Failed to get terminal reader: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Terminal Location
     */
    public function createLocation(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'display_name' => 'required|string|max:255',
                'address.line1' => 'required|string',
                'address.city' => 'required|string',
                'address.state' => 'required|string',
                'address.country' => 'required|string|size:2',
                'address.postal_code' => 'required|string',
            ]);

            $location = $this->stripe->terminal->locations->create([
                'display_name' => $validated['display_name'],
                'address' => $validated['address'],
            ]);

            Log::info('Terminal location created', ['location_id' => $location->id]);
            return response()->json($location);
        } catch (\Throwable $e) {
            Log::error('Failed to create terminal location: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Register a Terminal Reader
     */
    public function registerReader(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'location_id' => 'required|string',
                'registration_code' => 'required|string',
                'label' => 'required|string|max:255',
            ]);

            $reader = $this->stripe->terminal->readers->create([
                'location' => $validated['location_id'],
                'registration_code' => $validated['registration_code'],
                'label' => $validated['label']
            ]);

            Log::info('Terminal reader registered', ['reader_id' => $reader->id]);
            return response()->json($reader);
        } catch (\Throwable $e) {
            Log::error('Failed to register terminal reader: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Payment Intent for Terminal
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:50',
            ]);

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $validated['amount'],
                'currency' => 'usd',
                'payment_method_types' => ['card_present'],
                'capture_method' => 'automatic',
            ]);

            Log::info('Payment intent created', ['intent_id' => $intent->id]);
            return response()->json($intent);
        } catch (\Throwable $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Payment on Terminal Reader
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reader_id' => 'required|string',
            'payment_intent_id' => 'required|string',
        ]);

        $attempt = 0;
        $tries = 3;
        $shouldRetry = false;

        do {
            $attempt++;
            try {
                $reader = $this->stripe->terminal->readers->processPaymentIntent(
                    $validated['reader_id'],
                    ['payment_intent' => $validated['payment_intent_id']]
                );

                Log::info('Payment processing started', ['reader_id' => $validated['reader_id'], 'payment_intent_id' => $validated['payment_intent_id']]);
                return response()->json($reader);
            } catch (InvalidRequestException $e) {
                switch ($e->getStripeCode()) {
                    case 'terminal_reader_timeout':
                        if ($attempt == $tries) {
                            $shouldRetry = false;
                            Log::error('Terminal reader timeout after ' . $tries . ' attempts');
                            return response()->json(['error' => $e->getMessage()], 500);
                        } else {
                            $shouldRetry = true;
                        }
                        break;
                    case 'terminal_reader_offline':
                        $shouldRetry = false;
                        Log::error('Terminal reader offline');
                        return response()->json(['error' => 'Reader is offline. Please check connection.'], 500);
                    case 'terminal_reader_busy':
                        $shouldRetry = false;
                        Log::warning('Terminal reader busy');
                        return response()->json(['error' => 'Reader is busy processing another request.'], 500);
                    case 'intent_invalid_state':
                        $shouldRetry = false;
                        $paymentIntent = $this->stripe->paymentIntents->retrieve($validated['payment_intent_id']);
                        Log::error('Payment intent invalid state', ['status' => $paymentIntent->status]);
                        return response()->json(['error' => 'PaymentIntent is already in ' . $paymentIntent->status . ' state.'], 500);
                    default:
                        $shouldRetry = false;
                        Log::error('Payment processing error: ' . $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                }
            } catch (\Throwable $e) {
                Log::error('Unexpected payment processing error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } while ($shouldRetry);

        // Fallback in case loop exits without returning
        return response()->json(['error' => 'Payment processing failed'], 500);
    }

    /**
     * Simulate Payment (for testing)
     */
    public function simulatePayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reader_id' => 'required|string',
                'card_number' => 'nullable|string',
            ]);

            $params = [
                'card_present' => [
                    'number' => $validated['card_number'] ?? '4242424242424242'
                ],
                'type' => 'card_present'
            ];

            $reader = $this->stripe->testHelpers->terminal->readers->presentPaymentMethod(
                $validated['reader_id'],
                $params
            );

            Log::info('Payment simulated', ['reader_id' => $validated['reader_id']]);
            return response()->json($reader);
        } catch (\Throwable $e) {
            Log::error('Failed to simulate payment: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Capture Payment Intent
     */
    public function capturePaymentIntent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $intent = $this->stripe->paymentIntents->capture($validated['payment_intent_id']);

            Log::info('Payment intent captured', ['intent_id' => $intent->id]);
            return response()->json($intent);
        } catch (\Throwable $e) {
            Log::error('Failed to capture payment intent: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check Payment Intent Status (for polling)
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $intent = $this->stripe->paymentIntents->retrieve($validated['payment_intent_id']);

            return response()->json([
                'status' => $intent->status,
                'amount' => $intent->amount,
                'id' => $intent->id
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to check payment status: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel Payment on Reader
     */
    public function cancelPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reader_id' => 'required|string',
                'payment_intent_id' => 'nullable|string',
            ]);

            // Cancel the reader action
            $reader = $this->stripe->terminal->readers->cancelAction($validated['reader_id']);

            // Also cancel the payment intent if provided
            if (!empty($validated['payment_intent_id'])) {
                $intent = $this->stripe->paymentIntents->cancel($validated['payment_intent_id']);
            }

            Log::info('Payment cancelled', ['reader_id' => $validated['reader_id']]);
            return response()->json($reader);
        } catch (\Throwable $e) {
            Log::error('Cancel payment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Payment Intent for Shipping Cost
     */
    public function createShippingPaymentIntent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:50',
                'description' => 'nullable|string|max:255',
                'service_type' => 'nullable|string',
                'carrier' => 'nullable|string',
            ]);

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $validated['amount'],
                'currency' => 'usd',
                'payment_method_types' => ['card_present'],
                'capture_method' => 'automatic',
                'description' => $validated['description'] ?? 'Shipping Label Payment',
                'metadata' => [
                    'type' => 'shipping_payment',
                    'service_type' => $validated['service_type'] ?? '',
                    'carrier' => $validated['carrier'] ?? '',
                ]
            ]);

            Log::info('Shipping payment intent created', ['intent_id' => $intent->id]);
            return response()->json($intent);
        } catch (\Throwable $e) {
            Log::error('Create shipping payment intent error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Shipping Payment on Terminal
     */
    public function processShippingPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reader_id' => 'required|string',
                'payment_intent_id' => 'required|string',
            ]);

            Log::info('Processing shipping payment on reader', [
                'reader_id' => $validated['reader_id'],
                'payment_intent_id' => $validated['payment_intent_id']
            ]);

            $reader = $this->stripe->terminal->readers->processPaymentIntent(
                $validated['reader_id'],
                ['payment_intent' => $validated['payment_intent_id']]
            );

            Log::info('Payment processing started on reader', ['reader_action' => $reader->action ?? null]);
            return response()->json($reader);
        } catch (\Throwable $e) {
            Log::error('Process shipping payment error: ' . $e->getMessage(), [
                'reader_id' => $request->input('reader_id'),
                'payment_intent_id' => $request->input('payment_intent_id'),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Payment Success and Return Details
     */
    public function verifyShippingPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $intent = $this->stripe->paymentIntents->retrieve($validated['payment_intent_id']);

            // Log the actual status for debugging
            Log::info('Payment Intent Status: ' . $intent->status, [
                'payment_intent_id' => $intent->id,
                'status' => $intent->status,
                'amount' => $intent->amount,
                'charges' => count($intent->charges->data ?? [])
            ]);

            if ($intent->status === 'succeeded') {
                return response()->json([
                    'success' => true,
                    'payment_intent' => $intent,
                    'status' => $intent->status,
                    'amount_paid' => $intent->amount / 100,
                    'payment_method_id' => $intent->payment_method,
                    'charges' => $intent->charges->data ?? []
                ]);
            } elseif (in_array($intent->status, ['processing', 'requires_capture'])) {
                // These are normal processing states - not failures
                return response()->json([
                    'success' => false,
                    'status' => $intent->status,
                    'payment_intent' => $intent,
                    'message' => 'Payment is being processed on the reader'
                ]);
            } else {
                // These could be actual failures or other states
                return response()->json([
                    'success' => false,
                    'status' => $intent->status,
                    'payment_intent' => $intent,
                    'message' => 'Payment status: ' . $intent->status
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Verify shipping payment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Refund a payment intent
     */
    public function refundPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_intent_id' => 'required|string',
                'amount' => 'nullable|numeric|min:0.01',
                'reason' => 'nullable|string|in:duplicate,fraudulent,requested_by_customer',
                'metadata' => 'nullable|array'
            ]);

            $paymentIntentId = $validated['payment_intent_id'];
            $amount = $validated['amount'] ?? null;
            $reason = $validated['reason'] ?? 'requested_by_customer';
            $metadata = $validated['metadata'] ?? [];

            // Get the payment intent to find the charge
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            if (!$paymentIntent) {
                return response()->json(['error' => 'Payment intent not found'], 404);
            }

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment intent must be succeeded to refund. Current status: ' . $paymentIntent->status], 400);
            }

            // Get the charge ID from latest_charge property
            $chargeId = $paymentIntent->latest_charge ?? null;

            if (!$chargeId) {
                return response()->json(['error' => 'No charge found for this payment intent'], 400);
            }

            // Create refund parameters
            $refundParams = [
                'charge' => $chargeId,
                'reason' => $reason,
                'metadata' => array_merge($metadata, [
                    'payment_intent_id' => $paymentIntentId,
                    'refunded_at' => now()->toISOString(),
                    'source' => 'shipment_void'
                ])
            ];

            // Add amount if specified (partial refund), otherwise full refund
            if ($amount !== null) {
                $refundParams['amount'] = (int) ($amount * 100); // Convert to cents
            }

            // Create the refund
            $refund = $this->stripe->refunds->create($refundParams);

            return response()->json([
                'success' => true,
                'refund' => $refund,
                'refund_id' => $refund->id,
                'amount_refunded' => $refund->amount / 100,
                'status' => $refund->status,
                'reason' => $refund->reason
            ]);
        } catch (InvalidRequestException $e) {
            Log::error('Stripe refund validation error: ' . $e->getMessage());
            return response()->json(['error' => 'Refund validation error: ' . $e->getMessage()], 400);
        } catch (\Throwable $e) {
            Log::error('Refund processing error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create payment intent with saved customer card
     * NEW: For automatic charging of saved customer payment methods
     */
    public function createPaymentIntentWithCustomer(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:50',
                'customer_id' => 'required|string',
                'description' => 'nullable|string|max:255',
                'service_type' => 'nullable|string',
                'carrier' => 'nullable|string',
            ]);

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $validated['amount'],
                'currency' => 'usd',
                'customer' => $validated['customer_id'],
                'payment_method_types' => ['card'],
                'off_session' => true,
                'confirm' => true,
                'description' => $validated['description'] ?? 'Shipping Label Payment',
                'metadata' => [
                    'type' => 'shipping_payment',
                    'service_type' => $validated['service_type'] ?? '',
                    'carrier' => $validated['carrier'] ?? '',
                ]
            ]);

            Log::info('Customer payment intent created', [
                'intent_id' => $intent->id,
                'customer_id' => $validated['customer_id'],
                'amount' => $validated['amount']
            ]);

            return response()->json($intent);
        } catch (\Throwable $e) {
            Log::error('Create customer payment intent error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Charge customer's default payment method
     * NEW: Direct charge to customer's saved card
     */
    public function chargeCustomer(Request $request): JsonResponse
    {
        Log::info('chargeCustomer method called', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:50',
                'customer_id' => 'required|string',
                'description' => 'nullable|string|max:255',
                'service_type' => 'nullable|string',
                'carrier' => 'nullable|string',
            ]);

            Log::info('Request validated successfully', ['validated' => $validated]);

            // Get customer's default payment method
            Log::info('Retrieving Stripe customer', ['customer_id' => $validated['customer_id']]);
            $customer = $this->stripe->customers->retrieve($validated['customer_id']);

            Log::info('Stripe customer retrieved', [
                'customer_id' => $customer->id,
                'has_default_pm' => isset($customer->invoice_settings->default_payment_method)
            ]);

            if (!isset($customer->invoice_settings->default_payment_method) || !$customer->invoice_settings->default_payment_method) {
                Log::warning('No default payment method found for customer', ['customer_id' => $validated['customer_id']]);
                return response()->json(['error' => 'No default payment method found for this customer'], 400);
            }

            Log::info('Creating payment intent', [
                'amount' => $validated['amount'],
                'customer' => $validated['customer_id'],
                'payment_method' => $customer->invoice_settings->default_payment_method
            ]);

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $validated['amount'],
                'currency' => 'usd',
                'customer' => $validated['customer_id'],
                'payment_method' => $customer->invoice_settings->default_payment_method,
                'off_session' => true,
                'confirm' => true,
                'description' => $validated['description'] ?? 'Shipping Label Payment',
                'metadata' => [
                    'type' => 'shipping_payment',
                    'service_type' => $validated['service_type'] ?? '',
                    'carrier' => $validated['carrier'] ?? '',
                ]
            ]);

            Log::info('Customer charged successfully', [
                'intent_id' => $intent->id,
                'customer_id' => $validated['customer_id'],
                'amount' => $validated['amount'],
                'status' => $intent->status
            ]);

            return response()->json([
                'success' => true,
                'payment_intent' => $intent,
                'status' => $intent->status,
                'amount_paid' => $intent->amount / 100,
                'payment_method_id' => $intent->payment_method,
            ]);
        } catch (\Throwable $e) {
            Log::error('Charge customer error', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
