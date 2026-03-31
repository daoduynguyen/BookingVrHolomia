<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $order;

    // Hàm này để nhận đơn hàng từ CheckoutController truyền sang
    public function __construct($order)
    {
        $this->order = $order;
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎟️ Vé Điện Tử Thực Tế Ảo - Holomia VR (Đơn #' . $this->order->id . ')',
        );
    }

    public function content(): Content
    {
        // Trỏ đúng vào file giao diện booking_confirmed.blade.php
        return new Content(
            view: 'emails.booking_confirmed', 
        );
    }
}