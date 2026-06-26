<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fastaar API Key
    |--------------------------------------------------------------------------
    |
    | Your Fastaar merchant API Key. Can be a live key (fk_live_...) or a
    | test/sandbox key (fk_test_...).
    |
    */
    'api_key' => env('FASTAAR_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Fastaar Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The signing secret used to verify HMAC signatures of webhooks received
    | from Fastaar.
    |
    */
    'webhook_secret' => env('FASTAAR_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Connection timeout in seconds when making requests to the Fastaar API.
    |
    */
    'timeout_seconds' => (int) env('FASTAAR_TIMEOUT_SECONDS', 15),

];
