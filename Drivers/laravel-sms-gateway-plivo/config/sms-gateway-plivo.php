<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Plivo API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Plivo SMS API (https://plivo.com). The auth id is
    | appended to the base URL to scope requests to your account and both values
    | are sent as HTTP Basic authentication on every request. There is no
    | default: a missing or empty value fails at driver resolution.
    |
    */

    'auth_id'    => env('SMS_GATEWAY_PLIVO_AUTH_ID'),
    'auth_token' => env('SMS_GATEWAY_PLIVO_AUTH_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Plivo driver sends requests to. The auth id is not part
    | of it; the driver appends it to the request path. Edit it here, or set the
    | matching environment variable, when a proxy or a sandbox environment
    | requires a different host. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_PLIVO_BASE_URL', 'https://api.plivo.com/v1/Account/'),

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
        'server' => (int) env('SMS_GATEWAY_PLIVO_SERVER_TIMEOUT', 5),
        'client' => (int) env('SMS_GATEWAY_PLIVO_CLIENT_TIMEOUT', 6),
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
        'times'              => (int) env('SMS_GATEWAY_PLIVO_RETRY_TIMES', 2),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_PLIVO_RETRY_SLEEP_MILLISECONDS', 100),
    ],

];
