<?php

return [
    'default_country_id' => 1,
    'story_display_duration' => 30, // seconds
    'currency' => [
        'api_key' => env('EXCHANGERATE_API_KEY'),
        'base' => 'USD', // The API uses USD as default base for free tier usually
        'system_base' => 'USD', // The internal currency for payments (Global Base)
        'fallback_rates' => [
            'USD' => 1,
            'SAR' => 3.75,
            'EGP' => 52.6534,
            'KWD' => 0.3066,
            'AED' => 3.6725,
            'QAR' => 3.64,
            'BHD' => 0.376,
            'OMR' => 0.3845,
            'JOD' => 0.709,
        ],
    ],
];
