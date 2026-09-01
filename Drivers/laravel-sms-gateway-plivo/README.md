# Laravel SMS Gateway — Plivo Driver

A [Plivo](https://plivo.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-plivo
```

The service provider auto-registers a `plivo` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=plivo
SMS_GATEWAY_PLIVO_AUTH_ID=your-auth-id
SMS_GATEWAY_PLIVO_AUTH_TOKEN=your-auth-token
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-plivo-config
# or
php artisan sms-gateway-plivo:install
```

## Usage

With `SMS_GATEWAY_DRIVER=plivo`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'src' => '14151234567',
    'dst' => '14157654321',
    'text' => 'Hello from Plivo',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('plivo')->send($data);
```

`send()` posts to `POST Message/`, JSON. The payload goes straight to Plivo, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Plivo endpoint:

```php
$response = SmsGateway::driver('plivo')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `plivo` and the HTTP request and response.

## Configuration

`config/sms-gateway-plivo.php`:

- `auth_id` / `auth_token` — your Plivo credentials (`SMS_GATEWAY_PLIVO_AUTH_ID`, `SMS_GATEWAY_PLIVO_AUTH_TOKEN`), sent as HTTP Basic authentication; the auth id also scopes the default base URL to your account
- `base_url` — the endpoint (`SMS_GATEWAY_PLIVO_BASE_URL`), defaulting to the account-scoped `https://api.plivo.com/v1/Account/{auth_id}/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-plivo`.

## License

MIT. See [LICENSE](LICENSE).
