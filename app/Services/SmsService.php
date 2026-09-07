<?php

namespace App\Services;

use App\Models\Investigation;
use App\Models\Lab;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?Lab $lab;

    public function __construct(?Lab $lab = null)
    {
        $this->lab = $lab;
    }

    public function isEnabled(): bool
    {
        if ($this->lab) {
            return (bool) $this->lab->enable_sms;
        }
        return (bool) config('services.sms.enabled', false);
    }

    /**
     * Send SMS via configured provider (BulkSMSGateway, Twilio, Fast2SMS, Msg91, or Log)
     */
    public function send(string $mobileNumber, string $message, string $templateId = ''): bool
    {
        $mobileNumber = preg_replace('/[^0-9]/', '', $mobileNumber);
        if (strlen($mobileNumber) < 10) {
            return false;
        }

        $provider = $this->lab?->sms_provider ?: config('services.sms.provider', 'bulksmsgateway');
        $apiKey = $this->lab?->sms_api_key ?: config('services.sms.api_key', '');
        $senderId = $this->lab?->sms_sender_id ?: config('services.sms.sender', 'RBJLIS');

        Log::info("Dispatching SMS to {$mobileNumber} via {$provider}: {$message}");

        if (!$this->isEnabled()) {
            Log::info("SMS disabled for lab. Message logged only.");
            return true;
        }

        try {
            switch (strtolower($provider)) {
                case 'fast2sms':
                    $response = Http::withHeaders([
                        'authorization' => $apiKey,
                    ])->timeout(10)->post('https://www.fast2sms.com/dev/bulkV2', [
                        'route' => 'v3',
                        'sender_id' => $senderId ?: 'TXTIND',
                        'message' => $message,
                        'language' => 'english',
                        'flash' => 0,
                        'numbers' => $mobileNumber,
                    ]);
                    return $response->successful();

                case 'msg91':
                    $response = Http::withHeaders([
                        'authkey' => $apiKey,
                    ])->timeout(10)->post('https://api.msg91.com/api/v2/sendsms', [
                        'sender' => $senderId,
                        'route' => '4',
                        'country' => '91',
                        'sms' => [
                            ['message' => $message, 'to' => [$mobileNumber]]
                        ]
                    ]);
                    return $response->successful();

                case 'twilio':
                    $sid = config('services.twilio.sid');
                    $token = $apiKey ?: config('services.twilio.token');
                    $from = $senderId ?: config('services.twilio.from');
                    $response = Http::withBasicAuth($sid, $token)
                        ->asForm()
                        ->timeout(10)
                        ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                            'From' => $from,
                            'To' => '+' . (str_starts_with($mobileNumber, '91') ? $mobileNumber : '91' . $mobileNumber),
                            'Body' => $message,
                        ]);
                    return $response->successful();

                case 'bulksmsgateway':
                default:
                    $username = config('services.sms.username', 'smsbee');
                    $password = $apiKey ?: config('services.sms.password', '4796303');
                    $url = 'https://www.bulksmsgateway.in/sendmessage.php?' . http_build_query([
                        'user' => $username,
                        'password' => $password,
                        'mobile' => $mobileNumber,
                        'message' => $message,
                        'sender' => $senderId ?: 'RBJLIS',
                        'type' => '3',
                        'template_id' => $templateId ?: '1207163211794292521',
                    ]);
                    $response = Http::timeout(10)->get($url);
                    return $response->successful();
            }
        } catch (\Throwable $e) {
            Log::error('SMS Dispatch Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Booking & Payment Confirmation SMS
     */
    public function sendBookingConfirmation(Investigation $order): bool
    {
        $patient = $order->patient;
        if (!$patient || empty($patient->phone)) {
            return false;
        }

        $labName = $order->lab?->name ?: 'RBJ Diagnostics';
        $paid = $order->total_amount - $order->balance_amount;
        $message = "Dear {$patient->name}, your lab booking #INV-{$order->id} for Rs {$order->total_amount} with {$labName} is confirmed. Paid: Rs {$paid}, Bal: Rs {$order->balance_amount}. Track: " . url('/reports');
        $templateId = '1207163211794292521';

        return $this->send($patient->phone, $message, $templateId);
    }

    /**
     * Send Phlebotomy Sample Collected SMS
     */
    public function sendSampleCollected(Investigation $order, string $testName = ''): bool
    {
        $patient = $order->patient;
        if (!$patient || empty($patient->phone)) {
            return false;
        }

        $labName = $order->lab?->name ?: 'RBJ Diagnostics';
        $message = "Dear {$patient->name}, your specimen for {$testName} has been received by {$labName} laboratory technician for diagnostic testing.";
        return $this->send($patient->phone, $message);
    }

    /**
     * Send Report Ready & Verified SMS
     */
    public function sendReportReady(Investigation $order): bool
    {
        $patient = $order->patient;
        if (!$patient || empty($patient->phone)) {
            return false;
        }

        $labName = $order->lab?->name ?: 'RBJ Diagnostics';
        $downloadUrl = route('print.report', ['id' => $order->id]);
        $message = "Dear {$patient->name}, your pathology test report from {$labName} is now authorized and ready. Download PDF: {$downloadUrl}";
        return $this->send($patient->phone, $message);
    }
}
