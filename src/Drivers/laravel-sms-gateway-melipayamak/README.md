# Laravel SMS Gateway Melipayamak Driver

Melipayamak SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-melipayamak
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=melipayamak
SMS_GATEWAY_MELIPAYAMAK_USERNAME=your-username
SMS_GATEWAY_MELIPAYAMAK_PASSWORD=your-password
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-melipayamak-config
```

```php
<?php

declare(strict_types=1);

return [
    'username' => env('SMS_GATEWAY_MELIPAYAMAK_USERNAME'),
    'password' => env('SMS_GATEWAY_MELIPAYAMAK_PASSWORD'),
    'base_url' => env('SMS_GATEWAY_MELIPAYAMAK_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `melipayamak` |
| Default base URL | `https://rest.payamak-panel.com/api/` |
| `send()` endpoint | `POST SendSMS/SendSMS` |
| Authentication | `username` and `password` query parameters from `laravel-sms-gateway-melipayamak.username` and `laravel-sms-gateway-melipayamak.password` |
| Payload | Form data sent directly to Melipayamak |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('melipayamak')->send([
    'to'  => '09123456789',
    'from' => '50004000',
    'text' => 'Hello from Melipayamak',
]);
```

The payload is passed directly to Melipayamak, so use the fields expected by the Melipayamak API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('melipayamak')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-melipayamak` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
