<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public int $enableSms = 0;

    public function send(string $mobileNumber, string $message, string $templateId): bool
    {
        $username = config('services.sms.username', 'smsbee');
        $password = config('services.sms.password', '4796303');
        $sender = config('services.sms.sender', 'RBJLIS');

        $url = 'https://www.bulksmsgateway.in/sendmessage.php?' . http_build_query([
            'user' => $username,
            'password' => $password,
            'mobile' => $mobileNumber,
            'message' => $message,
            'sender' => $sender,
            'type' => '3',
            'template_id' => $templateId,
        ]);

        if ($this->enableSms === 1) {
            try {
                $response = Http::timeout(10)->get($url);
                Log::info('SMS Sent: ' . $response->body());
                return true;
            } catch (\Exception $e) {
                Log::error('SMS Send Error: ' . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function investigationCreate(string $name, $totalAmt, string $labName, $paid, $bal, string $mobileNumber): ?bool
    {
        $message = "Dear " . $name . ", Your order of Rs " . $totalAmt . " with " . $labName . " is processed. Paid: Rs " . $paid . " Bal: Rs " . $bal . " --RBJLIS";
        $templateId = '1207163211794292521';
        $this->send($mobileNumber, $message, $templateId);
        return null;
    }
}
