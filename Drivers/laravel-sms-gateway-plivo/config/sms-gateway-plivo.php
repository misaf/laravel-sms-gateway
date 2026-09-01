<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Plivo API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Plivo SMS API (https://plivo.com). The auth id scopes
    | the default base URL to your account and both values are sent as HTTP
    | Basic authentication on every request.
    |
    */

    'auth_id'    => env('SMS_GATEWAY_PLIVO_AUTH_ID', ''),
    'auth_token' => env('SMS_GATEWAY_PLIVO_AUTH_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Plivo driver sends requests to. Defaults to the
    | account-scoped "https://api.plivo.com/v1/Account/{auth_id}/". Override
    | when a proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_PLIVO_BASE_URL', ''),

];
