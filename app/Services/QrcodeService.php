<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrcodeService
{
    public function create(string $string): string
    {
        $url = url('/report/' . $string);
        $targetDir = public_path('img/uploads/qrcodes');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $relativeUrl = '/img/uploads/qrcodes/QRcode-' . $string . '.png';
        $fullPath = public_path('img/uploads/qrcodes/QRcode-' . $string . '.png');

        try {
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
        } catch (\Throwable $e) {
            // Fallback: simple placeholder if QR generation encounters any issue
            if (!file_exists($fullPath)) {
                $im = imagecreatetruecolor(120, 120);
                $bg = imagecolorallocate($im, 255, 255, 255);
                imagefill($im, 0, 0, $bg);
                imagepng($im, $fullPath);
                imagedestroy($im);
            }
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
            mkdir($targetDir, 0777, true);
        }

        $cleanRef = preg_replace('/[^a-zA-Z0-9_-]/', '_', $transactionRef);
        $relativeUrl = '/img/uploads/upiqrcodes/UPI-' . $cleanRef . '.png';
        $fullPath = public_path('img/uploads/upiqrcodes/UPI-' . $cleanRef . '.png');

        try {
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
        } catch (\Throwable $e) {
            // Fallback placeholder image
            if (!file_exists($fullPath)) {
                $im = imagecreatetruecolor(200, 200);
                $bg = imagecolorallocate($im, 255, 255, 255);
                imagefill($im, 0, 0, $bg);
                imagepng($im, $fullPath);
                imagedestroy($im);
            }
        }

        return $relativeUrl;
    }
}
