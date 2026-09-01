# Laravel SMS Gateway — Melipayamak Driver

A [Melipayamak](https://www.payamak-panel.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-melipayamak
```

The service provider auto-registers a `melipayamak` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=melipayamak
SMS_GATEWAY_MELIPAYAMAK_USERNAME=your-username
SMS_GATEWAY_MELIPAYAMAK_PASSWORD=your-password
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-melipayamak-config
# or
php artisan sms-gateway-melipayamak:install
```

## Usage

With `SMS_GATEWAY_DRIVER=melipayamak`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'to' => '09123456789',
    'from' => '50004000',
    'text' => 'Hello from Melipayamak',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('melipayamak')->send($data);
```

`send()` posts to `POST SendSMS/SendSMS`, form-encoded. The payload goes straight to Melipayamak, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Melipayamak endpoint:

```php
$response = SmsGateway::driver('melipayamak')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `melipayamak` and the HTTP request and response.

## Configuration

`config/sms-gateway-melipayamak.php`:

- `username` / `password` — your Melipayamak (Payamak-panel) credentials (`SMS_GATEWAY_MELIPAYAMAK_USERNAME`, `SMS_GATEWAY_MELIPAYAMAK_PASSWORD`), sent as the `username` and `password` query parameters
- `base_url` — the endpoint (`SMS_GATEWAY_MELIPAYAMAK_BASE_URL`), defaulting to `https://rest.payamak-panel.com/api/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-melipayamak`.

## License

MIT. See [LICENSE](LICENSE).
