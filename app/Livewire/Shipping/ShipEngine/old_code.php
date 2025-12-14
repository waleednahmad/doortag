<?php

// function addPriceComparison($formattedRates)
// {
//     $ratesCollection = collect($formattedRates);

//     // Group by service type → then key by carrier_id
//     $ratesByService = $ratesCollection
//         ->groupBy('service_type')
//         ->map(fn($serviceRates) => $serviceRates->keyBy('carrier_id'));

//     return collect($formattedRates)->map(function ($rate) use ($ratesByService) {

//         $serviceType = $rate['service_type'];
//         $serviceRates = $ratesByService->get($serviceType, collect());

//         // Target carriers (updated: carrier 2 is now primary)
//         $carrier1 = 'se-4121981'; // Secondary (Door tag)
//         $carrier2 = 'se-4084605'; // Primary (FedEx) - NOW PRIMARY

//         // Default comparison structure
//         $rate['price_comparison'] = [
//             'carrier_1_id'         => $carrier1,
//             'carrier_2_id'         => $carrier2,
//             'carrier_1_price'      => null,
//             'carrier_2_price'      => null,
//             'price_difference'     => null,
//             'difference_percentage' => null,
//             'is_cheaper'           => null,
//         ];

//         $carrier1Rate = $serviceRates->get($carrier1);
//         $carrier2Rate = $serviceRates->get($carrier2);

//         if ($carrier1Rate && $carrier2Rate) {
//             $rate['price_comparison']['carrier_1_price'] = $carrier1Rate['original_total'];
//             $rate['price_comparison']['carrier_2_price'] = $carrier2Rate['original_total'];

//             // Absolute difference
//             $difference = abs($carrier1Rate['original_total'] - $carrier2Rate['original_total']);
//             $rate['price_comparison']['price_difference'] = number_format($difference, 2);

//             // Correct percentage based on highest price
//             $base = max($carrier1Rate['original_total'], $carrier2Rate['original_total']);
//             $percentage = ($difference / $base) * 100;
//             $rate['price_comparison']['difference_percentage'] = round($percentage, 2);

//             // Determine which is cheaper
//             $rate['price_comparison']['is_cheaper'] =
//                 $carrier1Rate['original_total'] < $carrier2Rate['original_total'] ? 'carrier_1' : ($carrier2Rate['original_total'] < $carrier1Rate['original_total'] ? 'carrier_2' : 'equal');
//         }

//         return $rate;
//     })->values()->all();
// }


  // OLD FLOW: Terminal payment modal methods (commented out but kept for reference)
    /*
    public function showPaymentModal()
    {
        // Reset payment state
        $this->resetPaymentState();

        // Load available readers
        $this->loadReaders();

        // Show payment modal
        $this->showPaymentModal = true;
    }
    */

    // OLD FLOW: Reset payment state (commented out)
    /*
    public function resetPaymentState()
    {
        $this->paymentProcessing = false;
        $this->paymentRetryCount = 0;
        $this->paymentIntentId = null;
        $this->paymentSuccessful = false;
        $this->paymentError = null;
        $this->selectedReaderId = null;
    }
    */

    // OLD FLOW: Load terminal readers (commented out)
    /*
    public function loadReaders()
    {
        try {
            $response = Http::get(url('/api/terminal/list-readers'));
            $data = $response->json();

            if (isset($data['error'])) {
                $this->paymentError = 'Failed to load readers: ' . $data['error'];
            } else {
                $this->availableReaders = $data['data'] ?? [];

                // Auto-select the first online reader if available
                foreach ($this->availableReaders as $reader) {
                    if ($reader['status'] === 'online') {
                        $this->selectedReaderId = $reader['id'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->paymentError = 'Failed to load readers: ' . $e->getMessage();
        }
    }
    */

    // OLD FLOW: Process terminal payment (commented out)
    /*
    public function processPayment()
    {
        if (!$this->selectedReaderId) {
            $this->toast()->error('Please select a reader first.')->send();
            return;
        }

        $this->paymentProcessing = true;
        $this->paymentError = null;

        try {
            // Calculate the correct amount based on user authentication type
            $totalAmount = 0;

            if (auth('customer')->check()) {
                $authenticatedCustomer = Auth::user();
                // For admin customers: use customer_total, for regular customers: use end_user_total
                if ($authenticatedCustomer->is_admin) {
                    $shippingTotal = (float) str_replace(',', '', $this->customer_total ?? 0);
                } else {
                    $shippingTotal = (float) str_replace(',', '', $this->end_user_total ?? 0);
                }
                $totalAmount = ($shippingTotal + ($this->packagingAmount ?? 0) + ($this->taxAmount ?? 0)) * 100;
            } else {
                // For web users: use origin_total (base shipping cost) plus packaging
                $shippingTotal = (float) str_replace(',', '', $this->origin_total ?? 0);
                $totalAmount = ($shippingTotal + ($this->packagingAmount ?? 0) + ($this->taxAmount ?? 0)) * 100;
            }

            // Fallback to manual calculation if no totals are set
            if ($totalAmount <= 0) {
                $shippingAmount = (float) $this->selectedRate['shipping_amount']['amount'];
                $insuranceAmount = (float) ($this->selectedRate['insurance_amount']['amount'] ?? 0);
                $confirmationAmount = (float) ($this->selectedRate['confirmation_amount']['amount'] ?? 0);
                $otherAmount = (float) ($this->selectedRate['other_amount']['amount'] ?? 0);
                $totalAmount = ($shippingAmount + $insuranceAmount + $confirmationAmount + $otherAmount + ($this->packagingAmount ?? 0) + ($this->taxAmount ?? 0)) * 100;
            }


            // Create Payment Intent
            $intentResponse = Http::post(url('/api/terminal/shipping/create-payment-intent'), [
                'amount' => $totalAmount,
                'description' => 'Shipping Label - ' . ($this->selectedRate['service_type'] ?? 'Unknown'),
                'service_type' => $this->selectedRate['service_type'] ?? 'Unknown',
                'carrier' => $this->selectedRate['carrier_friendly_name'] ?? 'Unknown',
            ]);
            // Check if HTTP request was successful
            if (!$intentResponse->successful()) {
                throw new \Exception('Failed to create payment intent. HTTP Status: ' . $intentResponse->status());
            }

            $intentData = $intentResponse->json();

            if (isset($intentData['error'])) {
                throw new \Exception($intentData['error']);
            }

            if (!isset($intentData['id'])) {
                throw new \Exception('Payment intent ID not returned from server');
            }

            $this->paymentIntentId = $intentData['id'];

            // Process payment on reader
            $processResponse = Http::post(url('/api/terminal/shipping/process-payment'), [
                'reader_id' => $this->selectedReaderId,
                'payment_intent_id' => $this->paymentIntentId,
            ]);

            // Check if HTTP request was successful
            if (!$processResponse->successful()) {
                throw new \Exception('Failed to process payment. HTTP Status: ' . $processResponse->status());
            }

            $processData = $processResponse->json();

            if (isset($processData['error'])) {
                throw new \Exception($processData['error']);
            }

            // Start polling for payment completion
            $this->dispatch('payment-processing-started');
        } catch (\Exception $e) {
            info('Payment processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->paymentError = $e->getMessage();
            $this->paymentProcessing = false;
            $this->dispatch('payment-failed');
        }
    }
    */

    // OLD FLOW: Poll payment status (commented out)
    /*
    public function pollPaymentStatus()
    {
        // This will be called by JavaScript polling
        try {
            $verifyResponse = Http::post(url('/api/terminal/shipping/verify-payment'), [
                'payment_intent_id' => $this->paymentIntentId,
            ]);

            $verifyData = $verifyResponse->json();

            if (isset($verifyData['error'])) {
                throw new \Exception($verifyData['error']);
            }

            if ($verifyData['success']) {
                $this->paymentSuccessful = true;
                $this->paymentProcessing = false;
                $this->dispatch('payment-completed');

                // Store payment details and create the actual label
                $this->createActualLabel($verifyData['payment_intent']);
            } else {
                // Check the payment intent status to handle declined/cancelled cards
                $paymentIntent = $verifyData['payment_intent'] ?? null;
                $status = $verifyData['status'] ?? ($paymentIntent['status'] ?? 'unknown');

                // Only treat these as failures if they're explicitly failed states
                // requires_payment_method is normal when waiting for card presentation
                if (in_array($status, ['canceled', 'payment_failed'])) {
                    $this->paymentProcessing = false;

                    switch ($status) {
                        case 'canceled':
                            $this->paymentError = 'Payment was cancelled. Please try again.';
                            break;
                        case 'payment_failed':
                            $this->paymentError = 'Payment failed. Please try again.';
                            break;
                        default:
                            $this->paymentError = 'Payment was not successful. Please try again.';
                    }

                    $this->dispatch('payment-failed');
                }
                // Special handling for requires_payment_method after card interaction
                elseif ($status === 'requires_payment_method' && isset($paymentIntent['last_payment_error'])) {
                    // Only fail if there was actually an error (card declined, etc.)
                    $this->paymentProcessing = false;
                    $errorCode = $paymentIntent['last_payment_error']['code'] ?? 'unknown';
                    $errorMessage = $paymentIntent['last_payment_error']['message'] ?? 'Payment was declined.';

                    $this->paymentError = "Card declined: {$errorMessage}";
                    $this->dispatch('payment-failed');
                }
                // For statuses like 'processing', 'requires_capture', 'requires_payment_method' (without error), continue polling
            }
        } catch (\Exception $e) {
            // Only fail on actual errors, not on expected processing states
            if (strpos($e->getMessage(), 'processing') === false) {
                $this->paymentError = $e->getMessage();
                $this->paymentProcessing = false;
                $this->dispatch('payment-failed');
            }
        }
    }
    */

    // OLD FLOW: Retry terminal payment (commented out)
    /*
    public function retryPayment()
    {
        if ($this->paymentRetryCount >= $this->maxRetryAttempts) {
            $this->paymentError = 'Maximum retry attempts reached. Please try again later.';
            return;
        }

        $this->paymentRetryCount++;

        // Reset error state and immediately show processing again
        $this->paymentError = null;
        $this->paymentProcessing = true;

        $this->processPayment();
    }
    */