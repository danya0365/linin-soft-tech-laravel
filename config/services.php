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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'line' => [
        'channel_access_token' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'channel_secret' => env('LINE_CHANNEL_SECRET'),
    ],

    'wavespeed' => [
        'api_key' => env('WAVESPEED_API_KEY'),
        'base_url' => env('WAVESPEED_BASE_URL', 'https://llm.wavespeed.ai/v1'),
        'model' => env('WAVESPEED_LLM_MODEL', 'minimax/minimax-m2.7'),
        'max_iterations' => env('WAVESPEED_MAX_ITERATIONS', 5),
        'timeout' => env('WAVESPEED_TIMEOUT', 25), // วินาทีต่อ HTTP call
        'max_tokens' => env('WAVESPEED_MAX_TOKENS', 1024),
    ],

];
