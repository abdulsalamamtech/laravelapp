<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'paths' => ['*', 'api/*', 'sanctum/csrf-cookie', 'api/v1/register', 'api/v1/login', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // 'allowed_origins' => ['*'],
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'http://localhost:3000',
        'http://localhost:5000',
        'https://veriscore.app',
        'https://www.veriscore.app',
        'https://app.veriscore.app',
        'https://staging.veriscore.app',
        'https://v-frontend-gamma.vercel.app',
        'https://veriscore-main.vercel.app',
        'https://veriscore-staging.vercel.app',
        'https://veriscore-development.vercel.app',
        'https://v-frontend-onplaipz4-glued-veriscores-projects.vercel.app',
        'https://raking-footpad-spent.ngrok-free.dev',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 'supports_credentials' => false,
    'supports_credentials' => true,

];
