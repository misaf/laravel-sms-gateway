<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Melipayamak API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Melipayamak (Payamak-panel) REST API
    | (https://www.payamak-panel.com). They are sent as the "username" and
    | "password" query parameters on every request.
    |
    */

    'username' => env('SMS_GATEWAY_MELIPAYAMAK_USERNAME', ''),
    'password' => env('SMS_GATEWAY_MELIPAYAMAK_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Melipayamak driver sends requests to. Override only when
    | a proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_MELIPAYAMAK_BASE_URL', ''),

];
