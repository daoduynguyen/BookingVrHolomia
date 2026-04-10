<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use SerializesModels;

    public $order;
    public $statusName;
    public $messageBody;

    public function __construct($order, $statusName, $messageBody)
    {
        $this->order = $order;
        $this->statusName = $statusName;
        $this->messageBody = $messageBody;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cập nhật trạng thái đơn vé #' . $this->order->id . ' - Holomia VR',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_status',
        );
    }
}