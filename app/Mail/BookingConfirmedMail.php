<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use SerializesModels;

    public $order;

    public function __construct($order)
    {
        $order->loadMissing(['location', 'orderItems.ticket', 'slot']);
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎟️ Vé Điện Tử Thực Tế Ảo - Holomia VR (Đơn #' . $this->order->id . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_confirmed',
        );
    }
}