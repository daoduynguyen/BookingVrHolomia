<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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

    public function build()
    {
        return $this->subject('Cập nhật trạng thái đơn vé #' . $this->order->id . ' - Holomia VR')
            ->view('emails.order_status');
    }
}