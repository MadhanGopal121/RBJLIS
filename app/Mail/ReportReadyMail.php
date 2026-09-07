<?php

namespace App\Mail;

use App\Models\Investigation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Investigation $investigation;

    public function __construct(Investigation $investigation)
    {
        $this->investigation = $investigation->load([
            'patient',
            'lab',
            'investigationTests.diagnosticstest',
            'investigationTests.investigationTestResults.parameter'
        ]);
    }

    public function envelope(): Envelope
    {
        $labName = $this->investigation->lab?->name ?: 'RBJ Diagnostics';
        return new Envelope(
            subject: "Your Official Pathology Report is Ready - {$labName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report_ready',
        );
    }

    public function attachments(): array
    {
        try {
            $inv = $this->investigation;
            $barcode = '';
            $qrcode = '';
            $pdf = Pdf::loadView('print.report', compact('inv', 'barcode', 'qrcode'))
                ->setPaper('a4', 'portrait');

            return [
                Attachment::fromData(fn () => $pdf->output(), "Pathology_Report_INV-{$this->investigation->id}.pdf")
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            Log::error('Could not attach PDF report to email: ' . $e->getMessage());
            return [];
        }
    }
}
