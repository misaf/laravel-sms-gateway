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
    |
    */

    'default' => env('SMS_GATEWAY_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | Default HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | These values are used by all HTTP gateway drivers.
    |
    */

    'defaults' => [
        'timeout'         => env('SMS_GATEWAY_TIMEOUT', 10),
        'connect_timeout' => env('SMS_GATEWAY_CONNECT_TIMEOUT', 5),
    ],
];
