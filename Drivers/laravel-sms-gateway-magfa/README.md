# Laravel SMS Gateway — Magfa Driver

A [Magfa](https://magfa.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-magfa
```

The service provider auto-registers a `magfa` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=magfa
SMS_GATEWAY_MAGFA_USERNAME=your-username
SMS_GATEWAY_MAGFA_PASSWORD=your-password
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-magfa-config
# or
php artisan sms-gateway-magfa:install
```

## Usage

With `SMS_GATEWAY_DRIVER=magfa`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'senders' => ['3000'],
    'recipients' => ['09123456789'],
    'messages' => ['Hello from Magfa'],
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('magfa')->send($data);
```

`send()` posts to `POST send`, JSON. The payload goes straight to Magfa, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Magfa endpoint:

```php
$response = SmsGateway::driver('magfa')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `magfa` and the HTTP request and response.

## Configuration

`config/sms-gateway-magfa.php`:

- `username` / `password` — your Magfa credentials (`SMS_GATEWAY_MAGFA_USERNAME`, `SMS_GATEWAY_MAGFA_PASSWORD`), sent as HTTP Basic authentication
- `base_url` — the endpoint (`SMS_GATEWAY_MAGFA_BASE_URL`), defaulting to `https://sms.magfa.com/api/http/sms/v2/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-magfa`.

## License

MIT. See [LICENSE](LICENSE).
