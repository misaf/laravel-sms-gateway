<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | IPPanel API
    |--------------------------------------------------------------------------
    |
    | Credentials for the IPPanel HTTP API. They are sent as the "uname" and
    | "pass" query parameters on every request.
    |
    */

    'username' => env('SMS_GATEWAY_IPPANEL_USERNAME', ''),
    'password' => env('SMS_GATEWAY_IPPANEL_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the IPPanel driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_IPPANEL_BASE_URL', ''),

];
