<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Gateway Driver
    |--------------------------------------------------------------------------
    |
    | This option sets the default SMS gateway driver for requests. Install
    | the matching driver package or register a custom driver with extend().
    | The "null" driver ships with this package and sends nothing.
    |
    */

    'default' => env('SMS_GATEWAY_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Default HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | These values are used by all HTTP gateway drivers. The connect timeout
    | bounds the wait for the gateway, the request timeout is slightly larger
    | so a slow gateway loses the race, and failed attempts are retried.
    |
    */

    'defaults' => [
        'server_timeout'            => (int) env('SMS_GATEWAY_SERVER_TIMEOUT', 5),
        'client_timeout'            => (int) env('SMS_GATEWAY_CLIENT_TIMEOUT', 6),
        'retry_times'               => (int) env('SMS_GATEWAY_RETRY_TIMES', 2),
        'retry_sleep_milliseconds'  => (int) env('SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS', 100),
    ],

];
