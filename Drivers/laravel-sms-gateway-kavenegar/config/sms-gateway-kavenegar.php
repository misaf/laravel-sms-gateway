<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Kavenegar API
    |--------------------------------------------------------------------------
    | Credentials for the Kavenegar REST API (https://kavenegar.com). The api
    | key scopes the default base URL to your account and is sent as a header on
    | every request. It has no config default, so a missing
    | SMS_GATEWAY_KAVENEGAR_API_KEY environment variable fails at driver
    | resolution instead of sending an unauthenticated request.
    |
    */

    'api_key' => env('SMS_GATEWAY_KAVENEGAR_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    | The endpoint the Kavenegar driver sends requests to. This value is the
    | single source of truth for the host: edit it here, or set
    | SMS_GATEWAY_KAVENEGAR_BASE_URL, when a proxy or a sandbox environment
    | requires a different one. The API key is not part of it; the driver
    | appends it to the request path. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_KAVENEGAR_BASE_URL', 'https://api.kavenegar.com/v1/'),

    /*
    |--------------------------------------------------------------------------
    | Timeouts
    |--------------------------------------------------------------------------
    | "server" bounds the wait for a connection to the gateway, and "client" is
    | how long this application waits for the whole response. Keep the client
    | timeout above the server one, so a slow gateway loses the race instead of
    | being cut off mid-response. Both fall back to the core package defaults in
    | config/sms-gateway.php when the driver-specific variables are unset.
    |
    */

    'timeout' => [
        'server' => (int) env('SMS_GATEWAY_KAVENEGAR_SERVER_TIMEOUT', env('SMS_GATEWAY_SERVER_TIMEOUT', 5)),
        'client' => (int) env('SMS_GATEWAY_KAVENEGAR_CLIENT_TIMEOUT', env('SMS_GATEWAY_CLIENT_TIMEOUT', 6)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry
    |--------------------------------------------------------------------------
    | How a failed send is retried. Only transient faults are retried — a
    | connection failure or a server-side 5xx. A 4xx is never retried, since a
    | bad credential or a rate limit cannot resolve itself and would only burn
    | paid quota. "times" is the total number of attempts. Both fall back to the
    | core package defaults when the driver-specific variables are unset.
    |
    */

    'retry' => [
        'times'              => (int) env('SMS_GATEWAY_KAVENEGAR_RETRY_TIMES', env('SMS_GATEWAY_RETRY_TIMES', 2)),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_KAVENEGAR_RETRY_SLEEP_MILLISECONDS', env('SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS', 100)),
    ],

];
