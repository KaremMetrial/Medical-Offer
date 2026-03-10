<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string|null $apiUrl;
    protected string|null $apiKey;
    protected string|null $senderId;

    public function __construct()
    {
        // It's best practice to add these to config/services.php
        $this->apiUrl = config('services.sms.api_url', env('SMS_API_URL'));
        $this->apiKey = config('services.sms.api_key', env('SMS_API_KEY'));
        $this->senderId = config('services.sms.sender_id', env('SMS_SENDER_ID', 'MedicalOffer'));
    }

    /**
     * Send an SMS message to a specific phone number.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        // In local/testing environments without credentials, mock the send and log it.
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            Log::info("SMS Mock sent to {$to} [Sender: {$this->senderId}]: {$message}");
            return true;
        }

        try {
            /*
             * TODO: Adjust the payload structure based on your specific SMS provider's documentation.
             * (e.g., Twilio, Vonage, VictoryLink). This is a common generic implementation.
             */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->apiUrl, [
                'to'      => $to,
                'sender'  => $this->senderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS successfully sent to {$to}");
                return true;
            }

            Log::error("Failed to send SMS to {$to}. Error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
