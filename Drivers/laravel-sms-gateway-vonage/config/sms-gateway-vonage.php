<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Vonage API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Vonage SMS API (https://www.vonage.com). They are sent
    | as the "api_key" and "api_secret" query parameters on every request.
    |
    */

    'api_key'    => env('SMS_GATEWAY_VONAGE_API_KEY', ''),
    'api_secret' => env('SMS_GATEWAY_VONAGE_API_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Vonage driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_VONAGE_BASE_URL', ''),

];
