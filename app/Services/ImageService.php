<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageService
{
    public function upload(?UploadedFile $image, string $folder = 'uploads'): string
    {
        if (!$image || !$image->isValid()) {
            return '';
        }

        $targetDir = public_path('img/' . trim($folder, '/'));
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $extension = $image->getClientOriginalExtension() ?: 'jpg';
        $filename = md5(uniqid((string)rand(), true)) . '.' . $extension;

        $image->move($targetDir, $filename);

        return trim($folder, '/') . '/' . $filename;
    }

    public function seoUrl(string $data): string
    {
        $data = strtolower($data);
        $data = preg_replace('/[^a-z0-9 -]/', '', $data);
        $data = preg_replace('/[#$, -]+/', '-', $data);
        return trim($data, '-');
    }
}
