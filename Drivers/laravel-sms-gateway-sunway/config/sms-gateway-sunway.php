<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Sunway API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Sunway SMS API (https://www.sunwaysms.com). They are
    | sent as the "UserName" and "Password" query parameters on every request.
    |
    */

    'username' => env('SMS_GATEWAY_SUNWAY_USERNAME', ''),
    'password' => env('SMS_GATEWAY_SUNWAY_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Sunway driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_SUNWAY_BASE_URL', ''),

];
