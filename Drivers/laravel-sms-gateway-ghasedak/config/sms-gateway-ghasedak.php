<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Ghasedak API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Ghasedak SMS API (https://ghasedak.io). Leave the api
    | key empty to run the driver in local and testing environments; requests
    | are then still built, but no api key header is sent.
    |
    */

    'api_key' => env('SMS_GATEWAY_GHASEDAK_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Ghasedak driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_GHASEDAK_BASE_URL', ''),

];
