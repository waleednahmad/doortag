<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class TerminalController
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * List all Terminal Readers
     */
    public function listReaders()
    {
        try {
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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
