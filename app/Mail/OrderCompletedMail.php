<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Cảm ơn bạn đã trải nghiệm Holomia VR - Đơn #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_completed',
        );
    }
}