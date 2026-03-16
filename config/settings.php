<?php

return [
    'default_country_id' => 1,
    'story_display_duration' => 30, // seconds
    'currency' => [
        'api_key' => env('EXCHANGERATE_API_KEY'),
        'base' => 'USD', // The API uses USD as default base for free tier usually
        'system_base' => 'EGP', // The internal currency for payments
        'fallback_rates' => [
            'USD' => 1,
            'SAR' => 3.75,
            'EGP' => 52.35,
            'KWD' => 0.31,
            'AED' => 3.67,
            'QAR' => 3.64,
            'BHD' => 0.38,
            'OMR' => 0.38,
            'JOD' => 0.71,
        ],
    ],
];
