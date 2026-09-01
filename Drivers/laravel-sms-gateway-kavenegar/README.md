# Laravel SMS Gateway — Kavenegar Driver

A [Kavenegar](https://kavenegar.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-kavenegar
```

The service provider auto-registers a `kavenegar` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=kavenegar
SMS_GATEWAY_KAVENEGAR_API_KEY=your-api-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-kavenegar-config
# or
php artisan sms-gateway-kavenegar:install
```

## Usage

With `SMS_GATEWAY_DRIVER=kavenegar`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'receptor' => '09123456789',
    'message' => 'Hello from Kavenegar',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('kavenegar')->send($data);
```

`send()` posts to `POST sms/send.json`, form-encoded. The payload goes straight to Kavenegar, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Kavenegar endpoint:

```php
$response = SmsGateway::driver('kavenegar')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `kavenegar` and the HTTP request and response.

## Configuration

`config/sms-gateway-kavenegar.php`:

- `api_key` — your Kavenegar API key (`SMS_GATEWAY_KAVENEGAR_API_KEY`); it scopes the default base URL to your account
- `base_url` — the endpoint (`SMS_GATEWAY_KAVENEGAR_BASE_URL`), defaulting to the account-scoped `https://api.kavenegar.com/v1/{api_key}/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-kavenegar`.

## License

MIT. See [LICENSE](LICENSE).
