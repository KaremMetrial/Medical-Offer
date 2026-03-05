<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | This array contains the list of supported languages for your application.
    | The key is the language code and the value is the display name.
    | Add or remove languages as needed for your application.
    |
    */
    'codes' => [
        'ar',
        'en',
    ],


    'supported' => [
        'en' => 'English',
        'ar' => 'العربية (Arabic)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | This is the default language that will be used when no language is
    | specified or when a translation is not available.
    |
    */

    'default' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | RTL Languages
    |--------------------------------------------------------------------------
    |
    | List of languages that use Right-to-Left (RTL) text direction.
    | This can be useful for CSS styling or other RTL-specific logic.
    |
    */

    'rtl_languages' => [
        'ar', // Arabic
        'en', // Hebrew
    ],

    /*
    |--------------------------------------------------------------------------
    | Language Flags
    |--------------------------------------------------------------------------
    |
    | Optional: Add flag emojis or codes for each language for better UX.
    | This can be used in dropdowns or language switchers.
    |
    */

    'flags' => [
        'en' => '🇺🇸',
        'ar' => '🇸🇦',
    ],
];
