<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Textlocal API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Textlocal SMS API (https://www.textlocal.com). The
    | api key is sent as the "apikey" query parameter on every request.
    |
    */

    'api_key' => env('SMS_GATEWAY_TEXTLOCAL_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Textlocal driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_TEXTLOCAL_BASE_URL', ''),

];
