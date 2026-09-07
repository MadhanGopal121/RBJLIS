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
    public function generateUpiQr(string $vpa, string $payeeName, float $amount, string $transactionRef, string $note = 'Lab Investigation Bill'): string
    {
        $vpa = trim($vpa);
        if (empty($vpa)) {
            $vpa = 'rbjlab@upi';
        }

        $params = [
            'pa' => $vpa,
            'pn' => $payeeName ?: 'RBJ Diagnostics',
            'am' => number_format($amount, 2, '.', ''),
            'tr' => $transactionRef,
            'tn' => substr($note, 0, 50),
            'cu' => 'INR',
        ];

        $upiString = 'upi://pay?' . http_build_query($params);

        $targetDir = public_path('img/uploads/upiqrcodes');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $cleanRef = preg_replace('/[^a-zA-Z0-9_-]/', '_', $transactionRef);
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