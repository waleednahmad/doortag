<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\In;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

use function Laravel\Prompts\info;

class TerminalController
{
    private $stripe;
    private $testMode = false;

    public function __construct()
    {
        // Default to live mode
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * Set test mode for this instance
     */
    private function setTestMode($testMode = false)
    {
        $this->testMode = $testMode;
        if ($testMode) {
            $this->stripe = new StripeClient(env('STRIPE_TEST_SECRET'));
        } else {
            $this->stripe = new StripeClient(env('STRIPE_SECRET'));
        }
    }

    /**
     * List all Terminal Readers
     */
    public function listReaders()
    {
        try {
            $this->setTestMode(false);

            $readers = $this->stripe->terminal->readers->all(['limit' => 100]);
            return response()->json($readers);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a specific Terminal Reader
     */
    public function getReader(Request $request)
    {
        try {
            $reader = $this->stripe->terminal->readers->retrieve(
                $request->input('reader_id')
            );
            return response()->json($reader);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Terminal Location
     */
    public function createLocation()
    {
        try {
            $location = $this->stripe->terminal->locations->create([
                'display_name' => 'Main Location',
                'address' => [
                    'line1' => '1272 Valencia Street',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                    'postal_code' => '94110',
                ],
            ]);

            return response()->json($location);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Register a Terminal Reader
     */
    public function registerReader(Request $request)
    {
        try {
            $reader = $this->stripe->terminal->readers->create([
                'location' => $request->input('location_id'),
                'registration_code' => $request->input('registration_code', 'simulated-s700'),
                'label' => $request->input('label', 'S700 Reader')
            ]);

            return response()->json($reader);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a Payment Intent for Terminal
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            // Set test mode if requested
            $testMode = $request->input('test_mode', false);
            $this->setTestMode($testMode);

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $request->input('amount', 2000),
                'currency' => 'usd',
                'payment_method_types' => ['card_present'],
                'capture_method' => 'automatic',
            ]);

            return response()->json($intent);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Payment on Terminal Reader
     */
    public function processPayment(Request $request)
    {
        $attempt = 0;
        $tries = 3;
        $shouldRetry = false;

        do {
            $attempt++;
            try {
                $reader = $this->stripe->terminal->readers->processPaymentIntent(
                    $request->input('reader_id'),
                    ['payment_intent' => $request->input('payment_intent_id')]
                );

                return response()->json($reader);
            } catch (InvalidRequestException $e) {
                switch ($e->getStripeCode()) {
                    case 'terminal_reader_timeout':
                        // Temporary networking blip, automatically retry a few times.
                        if ($attempt == $tries) {
                            $shouldRetry = false;
                            return response()->json(['error' => $e->getMessage()], 500);
                        } else {
                            $shouldRetry = true;
                        }
                        break;
                    case 'terminal_reader_offline':
                        // Reader is offline
                        $shouldRetry = false;
                        return response()->json(['error' => 'Reader is offline. Please check connection.'], 500);
                    case 'terminal_reader_busy':
                        // Reader is currently busy
                        $shouldRetry = false;
                        return response()->json(['error' => 'Reader is busy processing another request.'], 500);
                    case 'intent_invalid_state':
                        // Check PaymentIntent status
                        $shouldRetry = false;
                        $paymentIntent = $this->stripe->paymentIntents->retrieve($request->input('payment_intent_id'));
                        return response()->json(['error' => 'PaymentIntent is already in ' . $paymentIntent->status . ' state.'], 500);
                    default:
                        $shouldRetry = false;
                        return response()->json(['error' => $e->getMessage()], 500);
                }
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } while ($shouldRetry);
    }

    /**
     * Simulate Payment (for testing)
     */
    public function simulatePayment(Request $request)
    {
        try {
            $params = [
                'card_present' => [
                    'number' => $request->input('card_number', '4242424242424242')
                ],
                'type' => 'card_present'
            ];

            $reader = $this->stripe->testHelpers->terminal->readers->presentPaymentMethod(
                $request->input('reader_id'),
                $params
            );

            return response()->json($reader);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Capture Payment Intent
     */
    public function capturePaymentIntent(Request $request)
    {
        try {
            $intent = $this->stripe->paymentIntents->capture(
                $request->input('payment_intent_id')
            );

            return response()->json($intent);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check Payment Intent Status (for polling)
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve(
                $request->input('payment_intent_id')
            );

            return response()->json([
                'status' => $intent->status,
                'amount' => $intent->amount,
                'id' => $intent->id
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel Payment on Reader
     */
    public function cancelPayment(Request $request)
    {
        try {
            // Cancel the reader action
            $reader = $this->stripe->terminal->readers->cancelAction(
                $request->input('reader_id')
            );

            // Also cancel the payment intent if needed
            if ($request->has('payment_intent_id')) {
                $intent = $this->stripe->paymentIntents->cancel(
                    $request->input('payment_intent_id')
                );
            }

            return response()->json($reader);
        } catch (\Throwable $e) {
            info('Cancel payment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Payment Intent for Shipping Cost
     */
    public function createShippingPaymentIntent(Request $request)
    {
        try {
            // Always use test mode for now as requested
            $this->setTestMode(false);

            $amount = $request->input('amount'); // Amount in cents
            $description = $request->input('description', 'Shipping Label Payment');

            // Validate amount
            if (!$amount || $amount < 50) {
                throw new \Exception('Invalid amount. Minimum amount is $0.50');
            }

            $intent = $this->stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['card_present'],
                'capture_method' => 'automatic',
                'description' => $description,
                'metadata' => [
                    'type' => 'shipping_payment',
                    'service_type' => $request->input('service_type', ''),
                    'carrier' => $request->input('carrier', ''),
                ]
            ]);

            info('Payment intent created successfully', ['intent_id' => $intent->id]);

            return response()->json($intent);
        } catch (\Throwable $e) {
            info('Create shipping payment intent error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Shipping Payment on Terminal
     */
    public function processShippingPayment(Request $request)
    {
        try {
            // Always use test mode for now
            $this->setTestMode(false);

            $readerId = $request->input('reader_id');
            $paymentIntentId = $request->input('payment_intent_id');

            info('Processing shipping payment on reader', [
                'reader_id' => $readerId,
                'payment_intent_id' => $paymentIntentId
            ]);

            // Validate inputs
            if (!$readerId) {
                throw new \Exception('Reader ID is required');
            }

            if (!$paymentIntentId) {
                throw new \Exception('Payment Intent ID is required');
            }

            $reader = $this->stripe->terminal->readers->processPaymentIntent(
                $readerId,
                ['payment_intent' => $paymentIntentId]
            );

            info('Payment processing started on reader', ['reader_action' => $reader->action ?? null]);

            return response()->json($reader);
        } catch (\Throwable $e) {
            info('Process shipping payment error: ' . $e->getMessage(), [
                'reader_id' => $request->input('reader_id'),
                'payment_intent_id' => $request->input('payment_intent_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Payment Success and Return Details
     */
    public function verifyShippingPayment(Request $request)
    {
        try {
            // Always use test mode for now
            $this->setTestMode(false);

            $intent = $this->stripe->paymentIntents->retrieve(
                $request->input('payment_intent_id')
            );

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
    public function refundPayment(Request $request)
    {
        try {
            $this->setTestMode(false);

            $request->validate([
                'payment_intent_id' => 'required|string',
                'amount' => 'nullable|numeric|min:0.01',
                'reason' => 'nullable|string|in:duplicate,fraudulent,requested_by_customer',
                'metadata' => 'nullable|array'
            ]);

            $paymentIntentId = $request->input('payment_intent_id');
            $amount = $request->input('amount'); // Amount in dollars, will be converted to cents
            $reason = $request->input('reason', 'requested_by_customer');
            $metadata = $request->input('metadata', []);

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
}
