# Laravel SMS Gateway SMS.ir Driver

SMS.ir SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-smsir
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=smsir
SMS_GATEWAY_SMSIR_API_KEY=your-api-key
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-smsir-config
```

```php
<?php

declare(strict_types=1);

return [
    'api_key'  => env('SMS_GATEWAY_SMSIR_API_KEY'),
    'base_url' => env('SMS_GATEWAY_SMSIR_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `smsir` |
| Default base URL | `https://api.sms.ir/v1/` |
| `send()` endpoint | `POST send/bulk` |
| Authentication | `X-API-KEY` header when `laravel-sms-gateway-smsir.api_key` is configured |
| Payload | Sent directly to SMS.ir |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('smsir')->send([
    'mobile' => '09123456789',
    'message' => 'Hello from sms.ir',
]);
```

The payload is passed directly to SMS.ir, so use the fields expected by the SMS.ir API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('smsir')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-smsir` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
