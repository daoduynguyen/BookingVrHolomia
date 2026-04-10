<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $statusName;
    public $messageBody;

    public function __construct($order, $statusName, $messageBody)
    {
        $this->order = $order;
        $this->statusName = $statusName;
        $this->messageBody = $messageBody;
        $this->afterCommit();
    }

    // ✅ Đổi từ build() sang envelope() + content() cho nhất quán
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