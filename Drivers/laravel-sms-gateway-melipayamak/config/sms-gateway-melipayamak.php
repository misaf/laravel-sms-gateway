<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Melipayamak API
    |--------------------------------------------------------------------------
    | Credentials for the Melipayamak (Payamak-panel) REST API
    | (https://www.payamak-panel.com). They are sent as the "username" and
    | "password" query parameters on every request. They have no config
    | defaults, so a missing SMS_GATEWAY_MELIPAYAMAK_USERNAME or
    | SMS_GATEWAY_MELIPAYAMAK_PASSWORD environment variable fails at driver
    | resolution instead of sending an unauthenticated request.
    |
    */

    'username' => env('SMS_GATEWAY_MELIPAYAMAK_USERNAME'),
    'password' => env('SMS_GATEWAY_MELIPAYAMAK_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    | The endpoint the Melipayamak driver sends requests to. This value is the
    | single source of truth for the host: edit it here, or set
    | SMS_GATEWAY_MELIPAYAMAK_BASE_URL, when a proxy or a sandbox environment
    | requires a different one. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_MELIPAYAMAK_BASE_URL', 'https://rest.payamak-panel.com/api/'),

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
        'server' => (int) env('SMS_GATEWAY_MELIPAYAMAK_SERVER_TIMEOUT', env('SMS_GATEWAY_SERVER_TIMEOUT', 5)),
        'client' => (int) env('SMS_GATEWAY_MELIPAYAMAK_CLIENT_TIMEOUT', env('SMS_GATEWAY_CLIENT_TIMEOUT', 6)),
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
        'times'              => (int) env('SMS_GATEWAY_MELIPAYAMAK_RETRY_TIMES', env('SMS_GATEWAY_RETRY_TIMES', 2)),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_MELIPAYAMAK_RETRY_SLEEP_MILLISECONDS', env('SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS', 100)),
    ],

];
