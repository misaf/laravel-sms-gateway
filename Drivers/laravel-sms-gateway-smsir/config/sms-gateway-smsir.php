<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | SMS.ir API
    |--------------------------------------------------------------------------
    |
    | Credentials for the SMS.ir API (https://sms.ir). The api key is sent as
    | the "X-API-KEY" header on every request. Leave it empty to run the
    | driver in local and testing environments; no api key header is then sent.
    |
    */

    'api_key' => env('SMS_GATEWAY_SMSIR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the SMS.ir driver sends requests to. Override only when a
    | proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_SMSIR_BASE_URL', ''),

];
