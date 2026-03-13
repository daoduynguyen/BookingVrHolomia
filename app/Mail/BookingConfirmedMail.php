<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $qr;

    public function __construct(Order $order, $qr)
    {
        $this->order = $order;
        $this->qr = $qr;
    }

    public function build()
    {
        return $this->subject('🎟️ Xác nhận đặt vé thành công tại Holomia VR')
                    ->view('emails.booking_confirmed');
    }
}