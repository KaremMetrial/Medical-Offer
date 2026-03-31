<?php

return [
    /**
     * API Token Key (string)
     * Accepted value:
     * Live Token: https://myfatoorah.readme.io/docs/live-token
     * Test Token: https://myfatoorah.readme.io/docs/test-token
     */
    'api_key' => env('MYFATOORAH_API_KEY'),
    /**
     * Test Mode (boolean)
     * Accepted value: true for the test mode or false for the live mode
     */
    'is_test' => env('MYFATOORAH_IS_TEST', true),
    /**
     * Vendor Country ISO Code (string)
     * Accepted value: KWT, SAU, ARE, QAT, BHR, OMN, JOD, or EGY
     */
    'vc_code' => env('MYFATOORAH_VC_CODE', 'KWT'),
    /**
     * Save card (boolean)
     * Accepted value: true if you want to enable save card options
     * You should contact your account manager to enable this feature in your MyFatoorah account as well
     */
    'save_card' => true,
    /**
     * Webhook secret key (string)
     * Accepted value: https://docs.myfatoorah.com/docs/webhook-information
     * Enables webhook on your MyFatoorah account setting then paste the secret key here
     * The webhook endpoint is: https://{example.com}/myfatoorah/webhook
     */
    'webhook_secret_key' => env('MYFATOORAH_WEBHOOK_SECRET_KEY', ''),

    /**
     * Register Apple Pay (boolean)
     * Accepted value: true to show the Apple Pay button on the checkout page
     * First, verify your domain with Apple Pay before you set it to true
     * You can either follow the steps here: https://docs.myfatoorah.com/docs/apple-pay#verify-your-domain-with-apple-pay or contact the MyFatoorah support team (tech@myfatoorah.com)
     */
    'register_apple_pay' => false,
    /**
     * Supplier code (integer)
     * Accepted value: number or null
     * The invoice will be created without supplier information if the supplier is not active, not approved, or does not exist (null)
     */
    'supplier_code' => null,

    /**
     * Allowed MyFatoorah IP addresses for Webhooks
     */
    'allowed_webhook_ips' => [
        '94.205.109.214',
        '94.205.109.11',
        '95.216.59.183',
        '52.209.155.101',
        '52.17.20.129',
    ]
];

