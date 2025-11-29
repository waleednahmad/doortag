<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $shipmentData,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Shipment Receipt — Order #' .
                str_pad(
                    $this->shipmentData['orderNumber'] ?? '000000',
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            from: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shipment-receipt',
            with: $this->shipmentData,
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
