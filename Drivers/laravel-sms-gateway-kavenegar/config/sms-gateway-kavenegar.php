<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Kavenegar API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Kavenegar REST API (https://kavenegar.com). The api
    | key scopes the default base URL to your account and is sent as a header
    | on every request.
    |
    */

    'api_key' => env('SMS_GATEWAY_KAVENEGAR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Kavenegar driver sends requests to. Defaults to the
    | account-scoped "https://api.kavenegar.com/v1/{api_key}/". Override when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_KAVENEGAR_BASE_URL', ''),

];
