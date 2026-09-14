<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // MONO OPEN BANKING API Configuration
    'mono' => [
        'secret_key' => env('MONO_SECRET_KEY'),
        'base_url' => 'https://api.withmono.com/v2',
        'latest_base_url' => 'https://api.withmono.com/v3',
        // MONO_FRONTEND_CALLBACK
        'frontend_callback_url' => env('MONO_FRONTEND_CALLBACK', 'http://localhost:3000/api/mono/callback'),
        // Lookup
        'lookup_secret_key' => env('LOOKUP_MONO_SECRET_KEY'),
        'lookup_base_url' => env('LOOKUP_MONO_BASE_URL', 'https://api.withmono.com/v3'),
    ],

    // PAYSTACK PAYMENT API Configuration
    'paystack' => [
        'secret' => env('PAYSTACK_SECRET_KEY'),
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
        'callback_url' => env('PAYSTACK_CALLBACK_URL', 'http://localhost:3000/api/paystack/callback'),
        'webhook_max_retries' => env('PAYSTACK_WEBHOOK_MAX_RETRIES', 5),
    ],

    // FLUTTERWAVE PAYMENT API Configuration
    'flutterwave' => [
        'secret' => env('FLUTTERWAVE_SECRET_KEY'),
        'public' => env('FLUTTERWAVE_PUBLIC_KEY'),
        'url' => env('FLUTTERWAVE_PAYMENT_URL', 'https://developersandbox-api.flutterwave.com/v3'),
        'callback_url' => env('FLUTTERWAVE_CALLBACK_URL', 'http://localhost:3000/api/flutterwave/callback'),
        'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET'),
        'webhook_max_retries' => env('FLUTTERWAVE_WEBHOOK_MAX_RETRIES', 5),
        'success' => env('FLUTTERWAVE_REDIRECT_SUCCESS', 'https://veriscore.app/payment/success'),
        'error' => env('FLUTTERWAVE_REDIRECT_ERROR', 'https://veriscore.app/payment/error'),
    ],

    // Whatsapp Chat Configuration
    'whatsapp' => [
        'chat_number' => env('WHATAPP_CHAT_NUMBER', '2348130000000'),
    ],

    // Socialite OAuth Providers
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URL'),
    ],

];
