<?php

namespace App\Mail;

use App\Models\Investigation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Investigation $investigation;

    public function __construct(Investigation $investigation)
    {
        $this->investigation = $investigation->load(['patient', 'lab', 'investigationTests.diagnosticstest']);
    }

    public function envelope(): Envelope
    {
        $labName = $this->investigation->lab?->name ?: 'RBJ Diagnostics';
        return new Envelope(
            subject: "Diagnostic Order Confirmation #INV-{$this->investigation->id} - {$labName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
