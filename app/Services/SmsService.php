<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function sendOtp($phone)
    {
        try {
            $payload = [
                'api_token'    => env('IPROG_API_TOKEN'),
                'phone_number' => $phone,
                'message'      => "Your OTP code is :otp. It is valid for 5 minutes. Do not share this code with anyone."
            ];

            $response = Http::withOptions([
                'verify' => false, // Testing only
            ])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://sms.iprogtech.com/api/v1/otp/send_otp', $payload);

            Log::info('IPROG SMS Response: ' . $response->body());

            return $response->json();
        } catch (\Exception $e) {
            Log::error("IPROG SMS Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendMessage($phone, $message)
{
    try {
        $payload = [
            'api_token'    => env('IPROG_API_TOKEN'),
            'phone_number' => $phone,
            'message'      => $message,
        ];

        $response = Http::withOptions(['verify' => false])
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://sms.iprogtech.com/api/v1/sms_messages', $payload);

        if ($response->successful()) {
            Log::info('SMS sent successfully to ' . $phone);
            return [
                'success' => true,
                'response' => $response->json()
            ];
        } else {
            Log::error('Failed to send SMS: ' . $response->body());
            return [
                'success' => false,
                'error' => $response->body()
            ];
        }

    } catch (\Exception $e) {
        Log::error('IPROG Contact Message Exception: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

}
