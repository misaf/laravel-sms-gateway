# Laravel SMS Gateway — Ghasedak Driver

A [Ghasedak](https://ghasedak.me) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

The service provider auto-registers a `ghasedak` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-ghasedak-config
# or
php artisan sms-gateway-ghasedak:install
```

## Usage

With `SMS_GATEWAY_DRIVER=ghasedak`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Here is a test message.',
    'receptor' => '+989119632587',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('ghasedak')->send($data);
```

`send()` posts to `POST sms/send/simple`. The payload goes straight to Ghasedak, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Ghasedak endpoint:

```php
$response = SmsGateway::driver('ghasedak')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `ghasedak` and the HTTP request and response.

## Configuration

`config/sms-gateway-ghasedak.php`:

- `api_key` — your Ghasedak API key (`SMS_GATEWAY_GHASEDAK_API_KEY`), sent as the `apikey` header; leave it empty in local and testing environments and no header is sent
- `base_url` — the endpoint (`SMS_GATEWAY_GHASEDAK_BASE_URL`), defaulting to `https://api.ghasedak.me/v2/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-ghasedak`.

## License

MIT. See [LICENSE](LICENSE).
