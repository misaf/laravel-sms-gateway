# Laravel SMS Gateway Textlocal Driver

Textlocal SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-textlocal
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=textlocal
SMS_GATEWAY_TEXTLOCAL_API_KEY=your-api-key
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-textlocal-config
```

```php
<?php

declare(strict_types=1);

return [
    'api_key'  => env('SMS_GATEWAY_TEXTLOCAL_API_KEY'),
    'base_url' => env('SMS_GATEWAY_TEXTLOCAL_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `textlocal` |
| Default base URL | `https://api.txtlocal.com/` |
| `send()` endpoint | `POST send/` |
| Authentication | `apikey` query parameter from `laravel-sms-gateway-textlocal.api_key` |
| Payload | Form data sent directly to Textlocal |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('textlocal')->send([
    'numbers' => '447123456789',
    'sender' => 'Laravel',
    'message' => 'Hello from Textlocal',
]);
```

The payload is passed directly to Textlocal, so use the fields expected by the Textlocal API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('textlocal')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-textlocal` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
