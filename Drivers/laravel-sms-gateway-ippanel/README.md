# Laravel SMS Gateway — IPPanel Driver

A [IPPanel](https://ippanel.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-ippanel
```

The service provider auto-registers a `ippanel` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=ippanel
SMS_GATEWAY_IPPANEL_USERNAME=your-username
SMS_GATEWAY_IPPANEL_PASSWORD=your-password
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-ippanel-config
# or
php artisan sms-gateway-ippanel:install
```

## Usage

With `SMS_GATEWAY_DRIVER=ippanel`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'op' => 'send',
    'from' => '3000505',
    'to' => '09123456789',
    'message' => 'Hello from IPPanel',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('ippanel')->send($data);
```

`send()` posts to `POST services.jspd`, form-encoded. The payload goes straight to IPPanel, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other IPPanel endpoint:

```php
$response = SmsGateway::driver('ippanel')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `ippanel` and the HTTP request and response.

## Configuration

`config/sms-gateway-ippanel.php`:

- `username` / `password` — your IPPanel credentials (`SMS_GATEWAY_IPPANEL_USERNAME`, `SMS_GATEWAY_IPPANEL_PASSWORD`), sent as the `uname` and `pass` query parameters
- `base_url` — the endpoint (`SMS_GATEWAY_IPPANEL_BASE_URL`), defaulting to `https://ippanel.com/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-ippanel`.

## License

MIT. See [LICENSE](LICENSE).
