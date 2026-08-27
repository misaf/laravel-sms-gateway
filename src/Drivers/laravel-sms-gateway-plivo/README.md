# Laravel SMS Gateway Plivo Driver

Plivo SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-plivo
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=plivo
SMS_GATEWAY_PLIVO_AUTH_ID=your-auth-id
SMS_GATEWAY_PLIVO_AUTH_TOKEN=your-auth-token
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-plivo-config
```

```php
<?php

declare(strict_types=1);

return [
    'auth_id'    => env('SMS_GATEWAY_PLIVO_AUTH_ID'),
    'auth_token' => env('SMS_GATEWAY_PLIVO_AUTH_TOKEN'),
    'base_url'   => env('SMS_GATEWAY_PLIVO_BASE_URL'),
];
```

By default, the auth ID is included in the base URL path. If you override `base_url`, include the account-specific path segment expected by Plivo.

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `plivo` |
| Default base URL | `https://api.plivo.com/v1/Account/{auth_id}/` |
| `send()` endpoint | `POST Message/` |
| Authentication | HTTP Basic auth from `laravel-sms-gateway-plivo.auth_id` and `laravel-sms-gateway-plivo.auth_token` |
| Payload | JSON data sent directly to Plivo |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('plivo')->send([
    'src' => '14151234567',
    'dst' => '14157654321',
    'text' => 'Hello from Plivo',
]);
```

The payload is passed directly to Plivo, so use the fields expected by the Plivo API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('plivo')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-plivo` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
