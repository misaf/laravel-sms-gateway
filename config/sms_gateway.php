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
    | Supported: "ghasedak", "sunway"
    |
    */

    'default' => env('SMS_GATEWAY_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Drivers
    |--------------------------------------------------------------------------
    |
    | Here, you can define all the SMS gateway 'drivers' for your application
    | along with their configurations.
    |
    | Supported drivers: "ghasedak", "sunway"
    |
    */

    'drivers' => [

        'ghasedak' => [
            'driver'     => 'ghasedak',
            'apiKey'     => env('SMS_GATEWAY_GHASEDAK_APIKEY'),
            'linenumber' => env('SMS_GATEWAY_GHASEDAK_LINENUMBER'),
        ],

        'sunway' => [
            'driver'         => 'sunway',
            'gateway'        => env('SMS_GATEWAY_SUNWAY_GATEWAY', 'https://sms.sunwaysms.com/smsws/HttpService.ashx'),
            'username'       => env('SMS_GATEWAY_SUNWAY_USERNAME'),
            'password'       => env('SMS_GATEWAY_SUNWAY_PASSWORD'),
            'special_number' => env('SMS_GATEWAY_SUNWAY_SPECIALNUMBER'),
        ],

    ],

];
