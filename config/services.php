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

    /*
    | reCAPTCHA v3 (classic). Scores run 0.0 (almost certainly a bot) to 1.0.
    | Submissions are never dropped for failing to verify — see
    | App\Services\Recaptcha — but set "block_below" to a score to start
    | rejecting the worst offenders outright.
    */
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'suspicious_below' => (float) env('RECAPTCHA_SUSPICIOUS_BELOW', 0.5),
        'block_below' => env('RECAPTCHA_BLOCK_BELOW') !== null
            ? (float) env('RECAPTCHA_BLOCK_BELOW')
            : null,
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

];
