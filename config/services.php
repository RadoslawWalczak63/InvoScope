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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_API_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_API_MODEL', 'gpt-4o-mini'),
        'model_max_tokens' => env('OPENAI_API_MODEL_MAX_TOKENS'),
        'temperature' => env('OPENAI_API_TEMPERATURE', 0.2),
    ],

    'copilot' => [
        'key' => env('COPILOT_API_KEY'),
        'base_url' => env('COPILOT_API_BASE_URL', 'https://models.github.ai'),
    ],
];
