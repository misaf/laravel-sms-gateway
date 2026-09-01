# Laravel SMS Gateway — Sunway Driver

A [Sunway](https://www.sunwaysms.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-sunway
```

The service provider auto-registers a `sunway` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=sunway
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-sunway-config
# or
php artisan sms-gateway-sunway:install
```

## Usage

With `SMS_GATEWAY_DRIVER=sunway`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'method' => 'SendSMS',
    'mobile' => '09123456789',
    'message' => 'Hello from Sunway',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('sunway')->send($data);
```

`send()` posts to `GET HttpService.ashx`, with the payload as query parameters. The payload goes straight to Sunway, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Sunway endpoint:

```php
$response = SmsGateway::driver('sunway')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `sunway` and the HTTP request and response.

## Configuration

`config/sms-gateway-sunway.php`:

- `username` / `password` — your Sunway credentials (`SMS_GATEWAY_SUNWAY_USERNAME`, `SMS_GATEWAY_SUNWAY_PASSWORD`), sent as the `UserName` and `Password` query parameters
- `base_url` — the endpoint (`SMS_GATEWAY_SUNWAY_BASE_URL`), defaulting to `https://sms.sunwaysms.com/smsws/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-sunway`.

## License

MIT. See [LICENSE](LICENSE).
