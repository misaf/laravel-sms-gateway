<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Magfa API
    |--------------------------------------------------------------------------
    | Credentials for the Magfa HTTP/SMS API (https://magfa.com). They are sent
    | as HTTP Basic authentication on every request. They have no config
    | defaults, so a missing SMS_GATEWAY_MAGFA_USERNAME or
    | SMS_GATEWAY_MAGFA_PASSWORD environment variable fails at driver resolution
    | instead of sending an unauthenticated request.
    |
    */

    'username' => env('SMS_GATEWAY_MAGFA_USERNAME'),
    'password' => env('SMS_GATEWAY_MAGFA_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    | The endpoint the Magfa driver sends requests to. This value is the
    | single source of truth for the host: edit it here, or set
    | SMS_GATEWAY_MAGFA_BASE_URL, when a proxy or a sandbox environment
    | requires a different one. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_MAGFA_BASE_URL', 'https://sms.magfa.com/api/http/sms/v2/'),

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
        'server' => (int) env('SMS_GATEWAY_MAGFA_SERVER_TIMEOUT', env('SMS_GATEWAY_SERVER_TIMEOUT', 5)),
        'client' => (int) env('SMS_GATEWAY_MAGFA_CLIENT_TIMEOUT', env('SMS_GATEWAY_CLIENT_TIMEOUT', 6)),
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
        'times'              => (int) env('SMS_GATEWAY_MAGFA_RETRY_TIMES', env('SMS_GATEWAY_RETRY_TIMES', 2)),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_MAGFA_RETRY_SLEEP_MILLISECONDS', env('SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS', 100)),
    ],

];
