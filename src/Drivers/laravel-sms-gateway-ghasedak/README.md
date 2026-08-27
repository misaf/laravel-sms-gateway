# Laravel SMS Gateway Ghasedak Driver

Ghasedak SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-ghasedak-config
```

```php
<?php

declare(strict_types=1);

return [
    'api_key'  => env('SMS_GATEWAY_GHASEDAK_API_KEY'),
    'base_url' => env('SMS_GATEWAY_GHASEDAK_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `ghasedak` |
| Default base URL | `https://api.ghasedak.me/v2/` |
| `send()` endpoint | `POST sms/send/simple` |
| Authentication | `apikey` header when `laravel-sms-gateway-ghasedak.api_key` is configured |
| Payload | Sent directly to Ghasedak |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('ghasedak')->send([
    'message' => 'Here is a test message.',
    'receptor' => '+989119632587',
]);
```

The payload is passed directly to Ghasedak, so use the fields expected by the Ghasedak API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('ghasedak')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-ghasedak` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
