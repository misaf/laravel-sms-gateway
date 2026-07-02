<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default SMS Gateway Driver
    |--------------------------------------------------------------------------
    |
    | This option sets the default SMS gateway driver for requests.
    | You may specify any of the other available drivers provided here.
    |
    | Supported: "ghasedak", "sunway", "kavenegar", "smsir", "twilio",
    | "vonage", "plivo", "messagebird", "textlocal", "melipayamak",
    | "ippanel", "magfa"
    |
    */

    'default' => env('SMS_GATEWAY_DRIVER', 'ghasedak'),

    /*
    |--------------------------------------------------------------------------
    | Default HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | These values are used by all HTTP gateway drivers.
    |
    */

    'defaults' => [
        'timeout'         => (int) env('SMS_GATEWAY_TIMEOUT', 10),
        'connect_timeout' => (int) env('SMS_GATEWAY_CONNECT_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Drivers
    |--------------------------------------------------------------------------
    |
    | Per-driver credentials and gateways are configured in config/services.php,
    | following Laravel's convention for third-party service credentials. Values
    | set there take precedence; anything defined here acts as a shared default,
    | and each driver falls back to its own built-in default gateway when
    | neither is set.
    |
    | Supported drivers: "ghasedak", "sunway", "kavenegar", "smsir",
    | "twilio", "vonage", "plivo", "messagebird", "textlocal",
    | "melipayamak", "ippanel", "magfa"
    |
    */

    'drivers' => [],

];
