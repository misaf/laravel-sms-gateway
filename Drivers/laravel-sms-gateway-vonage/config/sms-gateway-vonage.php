<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Vonage API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Vonage SMS API (https://www.vonage.com). They are sent
    | as the "api_key" and "api_secret" query parameters on every request. There
    | is no default: a missing or empty value fails at driver resolution.
    |
    */

    'api_key'    => env('SMS_GATEWAY_VONAGE_API_KEY'),
    'api_secret' => env('SMS_GATEWAY_VONAGE_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Vonage driver sends requests to. Edit it here, or set the
    | matching environment variable, when a proxy or a sandbox environment
    | requires a different host. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_VONAGE_BASE_URL', 'https://rest.nexmo.com/'),

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
        'server' => (int) env('SMS_GATEWAY_VONAGE_SERVER_TIMEOUT', 5),
        'client' => (int) env('SMS_GATEWAY_VONAGE_CLIENT_TIMEOUT', 6),
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
        'times'              => (int) env('SMS_GATEWAY_VONAGE_RETRY_TIMES', 2),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_VONAGE_RETRY_SLEEP_MILLISECONDS', 100),
    ],

];
