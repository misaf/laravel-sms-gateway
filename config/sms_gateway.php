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
            'apiKey'     => env('SMS_GATEWAY_GHASEDAK_APIKEY', ''),
            'linenumber' => env('SMS_GATEWAY_GHASEDAK_LINENUMBER'),
        ],

        'sunway' => [
            'gateway'        => env('SMS_GATEWAY_SUNWAY_GATEWAY', 'https://sms.sunwaysms.com/smsws/HttpService.ashx'),
            'username'       => env('SMS_GATEWAY_SUNWAY_USERNAME', ''),
            'password'       => env('SMS_GATEWAY_SUNWAY_PASSWORD', ''),
            'special_number' => env('SMS_GATEWAY_SUNWAY_SPECIALNUMBER'),
        ],

        'kavenegar' => [
            'apiKey'          => env('SMS_GATEWAY_KAVENEGAR_API_KEY', ''),
            'gateway'         => env('SMS_GATEWAY_KAVENEGAR_GATEWAY', 'https://api.kavenegar.com/v1/'),
            'timeout'         => (int) env('SMS_GATEWAY_KAVENEGAR_TIMEOUT', 10),
            'connect_timeout' => (int) env('SMS_GATEWAY_KAVENEGAR_CONNECT_TIMEOUT', 5),
        ],

        'smsir' => [
            'apiKey'          => env('SMS_GATEWAY_SMSIR_API_KEY', ''),
            'apiKeyHeader'    => env('SMS_GATEWAY_SMSIR_API_KEY_HEADER', 'X-API-KEY'),
            'gateway'         => env('SMS_GATEWAY_SMSIR_GATEWAY', 'https://api.sms.ir/v1/'),
            'timeout'         => (int) env('SMS_GATEWAY_SMSIR_TIMEOUT', 10),
            'connect_timeout' => (int) env('SMS_GATEWAY_SMSIR_CONNECT_TIMEOUT', 5),
        ],

    ],

];
