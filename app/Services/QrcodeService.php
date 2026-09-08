<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;

class QrcodeService
{
    public function create(string $string): string
    {
        $url = url('/report/' . $string);
        $targetDir = public_path('img/uploads/qrcodes');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $relativeUrl = '/img/uploads/qrcodes/QRcode-' . $string . '.png';
        $fullPath = public_path('img/uploads/qrcodes/QRcode-' . $string . '.png');

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $qrCode = new QrCode(
                    data: $url,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 120,
                    margin: 0
                );
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($fullPath);
                return $relativeUrl;
            }
        } catch (\Throwable $e) {
            Log::info('QR Generation fallback: ' . $e->getMessage());
        }

        // Fallback transparent PNG placeholder if GD is not present
        if (!file_exists($fullPath)) {
            @file_put_contents($fullPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        }

        return $relativeUrl;
    }

    /**
     * Generate NPCI Standard Dynamic UPI QR Code
     * (Scannable by Google Pay, PhonePe, Paytm, BHIM, Cred, AmazonPay)
     */
    public function generateUpiQr(string $vpa, string $payeeName, float $amount, string $transactionRef, string $note = 'Lab Bill'): string
    {
        $vpa = trim($vpa);
        if (empty($vpa)) {
            $vpa = 'rbjlab@upi';
        }

        // Sanitize payee name to remove HTML entities (&amp;) and invalid punctuation
        $cleanPayee = html_entity_decode($payeeName ?: 'RBJ Diagnostics', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleanPayee = preg_replace('/[^a-zA-Z0-9 ]/', ' ', $cleanPayee);
        $cleanPayee = trim(preg_replace('/\s+/', ' ', $cleanPayee));

        $cleanNote = html_entity_decode($note ?: 'Lab Bill', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleanNote = preg_replace('/[^a-zA-Z0-9 -]/', ' ', $cleanNote);
        $cleanNote = trim(preg_replace('/\s+/', ' ', $cleanNote));

        $cleanAmount = number_format($amount, 2, '.', '');
        $cleanRef = preg_replace('/[^a-zA-Z0-9_-]/', '_', $transactionRef);

        // Build standard NPCI URI
        $upiString = 'upi://pay?pa=' . rawurlencode($vpa)
                   . '&pn=' . rawurlencode($cleanPayee)
                   . '&am=' . $cleanAmount
                   . '&tr=' . rawurlencode($cleanRef)
                   . '&tn=' . rawurlencode(substr($cleanNote, 0, 30))
                   . '&cu=INR';

        $targetDir = public_path('img/uploads/upiqrcodes');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $relativeUrl = '/img/uploads/upiqrcodes/UPI-' . $cleanRef . '.png';
        $fullPath = public_path('img/uploads/upiqrcodes/UPI-' . $cleanRef . '.png');

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $qrCode = new QrCode(
                    data: $upiString,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 200,
                    margin: 5
                );
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($fullPath);
                return $relativeUrl;
            }
        } catch (\Throwable $e) {
            Log::info('UPI QR Generation fallback: ' . $e->getMessage());
        }

        // Fallback transparent PNG placeholder if GD is not present
        if (!file_exists($fullPath)) {
            @file_put_contents($fullPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
        }

        return $relativeUrl;
    }
}