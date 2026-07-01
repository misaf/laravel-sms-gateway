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
    | Supported: "ghasedak", "sunway", "kavenegar", "smsir"
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
        'timeout' => (int) env('SMS_GATEWAY_TIMEOUT', 10),
        'connect_timeout' => (int) env('SMS_GATEWAY_CONNECT_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Drivers
    |--------------------------------------------------------------------------
    |
    | Here, you can define all the SMS gateway 'drivers' for your application
    | along with their configurations.
    |
    | Supported drivers: "ghasedak", "sunway", "kavenegar", "smsir"
    |
    */

    'drivers' => [

        'ghasedak' => [
        ],

        'sunway' => [
            'gateway' => env('SMS_GATEWAY_SUNWAY_GATEWAY', 'https://sms.sunwaysms.com/smsws/HttpService.ashx'),
        ],

        'kavenegar' => [
            'gateway' => env('SMS_GATEWAY_KAVENEGAR_GATEWAY', 'https://api.kavenegar.com/v1/'),
        ],

        'smsir' => [
            'gateway' => env('SMS_GATEWAY_SMSIR_GATEWAY', 'https://api.sms.ir/v1/'),
        ],

    ],

];
