<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
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

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(120)
            ->margin(0)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        $result->saveToFile($fullPath);

        return $relativeUrl;
    }
}
