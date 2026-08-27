# Laravel SMS Gateway Kavenegar Driver

Kavenegar SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-kavenegar
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=kavenegar
SMS_GATEWAY_KAVENEGAR_API_KEY=your-api-key
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-kavenegar-config
```

```php
<?php

declare(strict_types=1);

return [
    'api_key'  => env('SMS_GATEWAY_KAVENEGAR_API_KEY'),
    'base_url' => env('SMS_GATEWAY_KAVENEGAR_BASE_URL'),
];
```

By default, the API key is included in the base URL path. If you override `base_url`, include the account-specific path segment expected by Kavenegar.

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `kavenegar` |
| Default base URL | `https://api.kavenegar.com/v1/{api_key}/` |
| `send()` endpoint | `POST sms/send.json` |
| Authentication | API key in the base URL path |
| Payload | Form data sent directly to Kavenegar |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('kavenegar')->send([
    'receptor' => '09123456789',
    'message' => 'Hello from kavenegar',
]);
```

The payload is passed directly to Kavenegar, so use the fields expected by the Kavenegar API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('kavenegar')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-kavenegar` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
