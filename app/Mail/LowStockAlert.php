<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $items; // Array barang yang stoknya habis

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[URGENT] ACTION REQUIRED: Stok Barang Habis (0)',
        );
    }

    public function content(): Content
    {
        // Kita pakai markdown simple view atau text biasa
        return new Content(
            view: 'emails.low_stock',
        );
    }
}