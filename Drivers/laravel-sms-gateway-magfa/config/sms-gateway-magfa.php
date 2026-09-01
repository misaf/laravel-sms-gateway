<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Magfa API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Magfa HTTP/SMS API (https://magfa.com). They are sent
    | as HTTP Basic authentication on every request. They have no config
    | defaults, so a missing SMS_GATEWAY_MAGFA_USERNAME or
    | SMS_GATEWAY_MAGFA_PASSWORD environment variable fails at driver resolution
    | instead of sending an unauthenticated request. There is no default: a
    | missing or empty value fails at driver resolution.
    |
    */

    'username' => env('SMS_GATEWAY_MAGFA_USERNAME'),
    'password' => env('SMS_GATEWAY_MAGFA_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Magfa driver sends requests to. Edit it here, or set the
    | matching environment variable, when a proxy or a sandbox environment
    | requires a different host. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_MAGFA_BASE_URL', 'https://sms.magfa.com/api/http/sms/v2/'),

    /*
    |--------------------------------------------------------------------------
    | Timeouts
    |--------------------------------------------------------------------------
    |
    | "server" bounds the wait for a connection to the gateway, "client" the
    | wait for the whole response. Keep the client timeout above the server one,
    | so a slow gateway loses the race instead of being cut off mid-response.
    |
    */

    'timeout' => [
        'server' => (int) env('SMS_GATEWAY_MAGFA_SERVER_TIMEOUT', 5),
        'client' => (int) env('SMS_GATEWAY_MAGFA_CLIENT_TIMEOUT', 6),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry
    |--------------------------------------------------------------------------
    |
    | Only transient faults are retried — a connection failure or a server-side
    | 5xx. A 4xx is never retried: a bad credential or a rate limit cannot
    | resolve itself and would only burn paid quota. "times" is the total number
    | of attempts.
    |
    */

    'retry' => [
        'times'              => (int) env('SMS_GATEWAY_MAGFA_RETRY_TIMES', 2),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_MAGFA_RETRY_SLEEP_MILLISECONDS', 100),
    ],

];
