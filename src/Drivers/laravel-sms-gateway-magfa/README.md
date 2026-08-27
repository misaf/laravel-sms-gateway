# Laravel SMS Gateway Magfa Driver

Magfa SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-magfa
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=magfa
SMS_GATEWAY_MAGFA_USERNAME=your-username
SMS_GATEWAY_MAGFA_PASSWORD=your-password
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-magfa-config
```

```php
<?php

declare(strict_types=1);

return [
    'username' => env('SMS_GATEWAY_MAGFA_USERNAME'),
    'password' => env('SMS_GATEWAY_MAGFA_PASSWORD'),
    'base_url' => env('SMS_GATEWAY_MAGFA_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `magfa` |
| Default base URL | `https://sms.magfa.com/api/http/sms/v2/` |
| `send()` endpoint | `POST send` |
| Authentication | HTTP Basic auth from `laravel-sms-gateway-magfa.username` and `laravel-sms-gateway-magfa.password` |
| Payload | JSON data sent directly to Magfa |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('magfa')->send([
    'senders'   => ['3000'],
    'recipients' => ['09123456789'],
    'messages'  => ['Hello from Magfa'],
]);
```

The payload is passed directly to Magfa, so use the fields expected by the Magfa API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('magfa')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-magfa` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
