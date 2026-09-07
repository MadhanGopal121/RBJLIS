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
}
