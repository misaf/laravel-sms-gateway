# Laravel SMS Gateway IPPanel Driver

IPPanel SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-ippanel
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=ippanel
SMS_GATEWAY_IPPANEL_USERNAME=your-username
SMS_GATEWAY_IPPANEL_PASSWORD=your-password
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-ippanel-config
```

```php
<?php

declare(strict_types=1);

return [
    'username' => env('SMS_GATEWAY_IPPANEL_USERNAME'),
    'password' => env('SMS_GATEWAY_IPPANEL_PASSWORD'),
    'base_url' => env('SMS_GATEWAY_IPPANEL_BASE_URL'),
];
```

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `ippanel` |
| Default base URL | `https://ippanel.com/` |
| `send()` endpoint | `POST services.jspd` |
| Authentication | `uname` and `pass` query parameters from `laravel-sms-gateway-ippanel.username` and `laravel-sms-gateway-ippanel.password` |
| Payload | Form data sent directly to IPPanel |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('ippanel')->send([
    'op'     => 'send',
    'from'   => '3000505',
    'to'     => '09123456789',
    'message' => 'Hello from IPPanel',
]);
```

The payload is passed directly to IPPanel, so use the fields expected by the IPPanel API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('ippanel')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-ippanel` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
