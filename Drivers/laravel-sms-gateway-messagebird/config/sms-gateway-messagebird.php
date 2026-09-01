<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | MessageBird API
    |--------------------------------------------------------------------------
    |
    | Credentials for the MessageBird SMS API (https://messagebird.com). The
    | access key is sent as the "Authorization: AccessKey {key}" header on
    | every request.
    |
    */

    'access_key' => env('SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the MessageBird driver sends requests to. Override only when
    | a proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_MESSAGEBIRD_BASE_URL', ''),

];
