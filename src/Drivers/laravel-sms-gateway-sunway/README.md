# Laravel SMS Gateway Sunway Driver

Sunway SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-sunway
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=sunway
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-sunway-config
```

```php
<?php

declare(strict_types=1);

return [
    'username' => env('SMS_GATEWAY_SUNWAY_USERNAME'),
    'password' => env('SMS_GATEWAY_SUNWAY_PASSWORD'),
    'base_url' => env('SMS_GATEWAY_SUNWAY_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `sunway` |
| Default base URL | `https://sms.sunwaysms.com/smsws/` |
| `send()` endpoint | `GET HttpService.ashx` |
| Authentication | `UserName` and `Password` query parameters from `laravel-sms-gateway-sunway.username` and `laravel-sms-gateway-sunway.password` |
| Payload | Query parameters sent directly to Sunway |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('sunway')->send([
    'method' => 'SendSMS',
    'mobile' => '09123456789',
    'message' => 'Hello, World!',
]);
```

The payload is passed directly to Sunway, so use the fields expected by the Sunway API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('sunway')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-sunway` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
