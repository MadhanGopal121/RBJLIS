<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    public function create(string $string): bool
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($string, $generator::TYPE_CODE_128, 2, 35);

        $targetDir = public_dir_path('img/uploads/barcode');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        file_put_contents($targetDir . '/' . $string . '.png', $barcodeData);
        return true;
    }
}

if (!function_exists('public_dir_path')) {
    function public_dir_path($path = '') {
        return public_path($path);
    }
}
