<?php

namespace App\Services\Payments;

use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MyFatoorahService
{
    protected $config;
    protected $mfObj;

    public function __construct()
    {
        $this->config = [
            'apiKey'    => config('myfatoorah.api_key'),
            'isTest'    => config('myfatoorah.is_test'),
            'vcCode'    => config('myfatoorah.vc_code'),
            'loggerObj' => storage_path('logs/myfatoorah.log')
        ];

        $this->mfObj = new MyFatoorahPaymentStatus($this->config);
    }


    /**
     * Create a payment invoice
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createInvoice(array $data)
    {
        try {
            $payment = $this->mfObj->getInvoiceURL($data, 0); 
            return $payment;
        } catch (\Exception $e) {
            Log::error('MyFatoorah createInvoice Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check payment status
     *
     * @param string $paymentId
     * @return array
     * @throws \Exception
     */
    public function getPaymentStatus($paymentId)
    {
        try {
            return $this->mfObj->getPaymentStatus($paymentId, 'PaymentId');
        } catch (\Exception $e) {
            Log::error('MyFatoorah getPaymentStatus Error: ' . $e->getMessage(), [
                'paymentId' => $paymentId
            ]);
            throw $e;
        }
    }

    /**
     * Validate webhook data
     *
     * @param array $data
     * @return array
     */
    public function parseWebhookPayload(array $data)
    {
        // MyFatoorah webhooks typically contain 'Data' object
        return $data['Data'] ?? $data;
    }

    /**
     * Handle idempotency
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function withIdempotency($key, callable $callback)
    {
        $lockKey = "payment_processing_{$key}";
        
        // Use a 10-minute lock to prevent duplicate processing
        if (Cache::has($lockKey)) {
            Log::info("Duplicate processing attempt detected for key: {$key}");
            return null;
        }

        Cache::put($lockKey, true, 600);

        try {
            return $callback();
        } catch (\Exception $e) {
            Cache::forget($lockKey);
            throw $e;
        }
    }
}
