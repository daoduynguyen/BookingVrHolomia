<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $order->loadMissing(['location', 'orderItems.ticket', 'slot', 'details.ticket']);
        $this->order = $order;
        $this->afterCommit();
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