<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Magfa API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Magfa HTTP/SMS API (https://magfa.com). They are sent
    | as HTTP Basic authentication on every request.
    |
    */

    'username' => env('SMS_GATEWAY_MAGFA_USERNAME', ''),
    'password' => env('SMS_GATEWAY_MAGFA_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Magfa driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_MAGFA_BASE_URL', ''),

];
