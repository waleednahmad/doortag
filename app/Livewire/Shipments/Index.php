<?php

namespace App\Livewire\Shipments;

use App\Services\ShipEngineService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions, WithPagination;

    public $labels = [];
    public $totalLabels = 0;
    public $currentPage = 1;
    public $perPage = -1; // Show all labels by default
    public $totalPages = 0;
    public $loading = false;
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $authUser = Auth::user();
        $userLabels = $authUser->shipments()->whereNotNull('label_id')->pluck('label_id')->toArray();
        try {
            $this->loading = true;
            $shipEngine = new ShipEngineService();
            $response = $shipEngine->getLabels($this->currentPage, $this->perPage, $this->sortBy, $this->sortDir);

            if (isset($response['status']) && $response['status'] === 'error') {
                $errorMessage = $response['errors'][0]['message'] ?? 'Failed to load labels';
                $this->toast()->error($errorMessage)->send();
                return;
            }
            $responseLabels = $response['labels'] ?? [];
            // Filter labels to include only those associated with the authenticated user
            $filteredLabels = array_filter($responseLabels, function ($label) use ($userLabels) {
                return in_array($label['label_id'], $userLabels);
            });
            // Here iwant to add three values to each label from the shipments table: origin_total, customer_total, end_user_total
            foreach ($filteredLabels as &$label) {
                $shipment = $authUser->shipments()->where('label_id', $label['label_id'])->first();
                if ($shipment) {
                    $requestData = json_decode($shipment->request_data, true);
                    $label['id'] = $shipment->id;
                    $label['origin_total'] = $shipment->origin_total;
                    $label['customer_total'] = $shipment->customer_total;
                    $label['end_user_total'] = $shipment->end_user_total;
                    $label['signature'] = $shipment->signature_path;
                    $label['ship_to_address_country_full_name'] = $requestData['ship_to_address_country_full_name'] ?? null;
                    $label['stripe_amount_paid'] = $shipment->stripe_amount_paid;
                } else {
                    $label['origin_total'] = null;
                    $label['customer_total'] = null;
                    $label['end_user_total'] = null;
                }
            }


            $this->labels = array_values($filteredLabels);
            $this->totalLabels = count($this->labels);
            $this->totalPages = 1;
            // $this->totalLabels = $response['total'] ?? 0;
            // $this->totalPages = $response['pages'] ?? 0;
        } catch (\Exception $e) {
            $this->toast()->error('Failed to load labels: ' . $e->getMessage())->send();
            $this->labels = [];
        } finally {
            $this->loading = false;
        }
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadLabels();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadLabels();
        }
    }

    public function goToPage($page)
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
        $this->loadLabels();
    }

    public function refreshLabels()
    {
        $this->loadLabels();
        $this->toast()->success('Labels refreshed successfully!')->send();
    }

    public function voidLabel($labelId)
    {
        try {
            $this->loading = true;

            // Get the shipment record first to check for payment info
            $authUser = Auth::user();
            $shipment = $authUser->shipments()->where('label_id', $labelId)->first();

            // Void the label first
            $shipengine = new ShipEngineService();
            $response = $shipengine->voidLabel($labelId);

            // Handle response and provide feedback to the user
            if (isset($response['errors'])) {
                $this->toast()->error('Failed to void the label: ' . $response['errors'][0]['message'])->send();
                return;
            } elseif (isset($response['approved']) && $response['approved'] == 1) {
                // Label voided successfully, now process refund if payment exists
                $refundProcessed = false;
                $refundMessage = '';

                if ($shipment && $shipment->stripe_payment_intent_id && $shipment->stripe_amount_paid > 0) {
                    try {
                        // Process Stripe refund
                        $refundResponse = \Illuminate\Support\Facades\Http::post(url('/api/terminal/refund-payment'), [
                            'payment_intent_id' => $shipment->stripe_payment_intent_id,
                            'reason' => 'requested_by_customer',
                            'metadata' => [
                                'shipment_id' => $shipment->id,
                                'label_id' => $labelId,
                                'voided_by' => $authUser->id ?? $authUser->email,
                                'void_reason' => 'Label voided'
                            ]
                        ]);

                        if ($refundResponse->successful()) {
                            $refundData = $refundResponse->json();
                            if ($refundData['success']) {
                                // Update shipment record with refund info
                                $shipment->update([
                                    'stripe_payment_status' => 'refunded',
                                    'stripe_response' => array_merge(
                                        $shipment->stripe_response ?? [],
                                        ['refund' => $refundData['refund']]
                                    )
                                ]);

                                $refundProcessed = true;
                                $refundMessage = ' Payment of $' . number_format($refundData['amount_refunded'], 2) . ' has been refunded.';
                            } else {
                                $refundMessage = ' Warning: Label voided but refund failed.';
                            }
                        } else {
                            $refundMessage = ' Warning: Label voided but refund processing encountered an error.';
                        }
                    } catch (\Exception $refundException) {
                        Log::error('Refund processing error during void: ' . $refundException->getMessage(), [
                            'shipment_id' => $shipment->id,
                            'payment_intent_id' => $shipment->stripe_payment_intent_id
                        ]);
                        $refundMessage = ' Warning: Label voided but automatic refund failed. Please process refund manually.';
                    }
                }

                $successMessage = 'Label voided successfully.' . $refundMessage;
                $this->toast()->success($successMessage)->send();

                // Refresh the labels list
                $this->loadLabels();
            } else {
                $this->toast()->warning('Label void request processed but status unclear.')->send();
            }
        } catch (\Exception $e) {
            Log::error('Void label error: ' . $e->getMessage(), [
                'label_id' => $labelId,
                'user_id' => Auth::id()
            ]);
            $this->toast()->error('Failed to void label: ' . $e->getMessage())->send();
        } finally {
            $this->loading = false;
        }
    }

    public function trackingNumberCopied()
    {
        $this->toast()->success('Tracking number copied to clipboard!')->send();
    }

    public function redirectToTracking($trackingUrl)
    {
        if (!empty($trackingUrl)) {
            $this->dispatch('redirect-to-tracking', url: $trackingUrl);
        } else {
            $this->toast()->error('Tracking URL not available for this shipment.')->send();
        }
    }


    public function render()
    {
        return view('livewire.shipments.index');
    }
}
