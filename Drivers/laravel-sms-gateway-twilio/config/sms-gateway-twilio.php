<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Twilio API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Twilio SMS API (https://twilio.com). The account sid
    | is appended to the base URL to scope requests to your account and both
    | values are sent as HTTP Basic authentication on every request. There is no
    | default: a missing or empty value fails at driver resolution.
    |
    */

    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID'),
    'auth_token'  => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Twilio driver sends requests to. The account sid is not
    | part of it; the driver appends it to the request path. Edit it here, or
    | set the matching environment variable, when a proxy or a sandbox
    | environment requires a different host. It may not be empty.
    |
    */

    'base_url' => env('SMS_GATEWAY_TWILIO_BASE_URL', 'https://api.twilio.com/2010-04-01/Accounts/'),

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
        'server' => (int) env('SMS_GATEWAY_TWILIO_SERVER_TIMEOUT', 5),
        'client' => (int) env('SMS_GATEWAY_TWILIO_CLIENT_TIMEOUT', 6),
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
        'times'              => (int) env('SMS_GATEWAY_TWILIO_RETRY_TIMES', 2),
        'sleep_milliseconds' => (int) env('SMS_GATEWAY_TWILIO_RETRY_SLEEP_MILLISECONDS', 100),
    ],

];
