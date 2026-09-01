# Laravel SMS Gateway — Textlocal Driver

A [Textlocal](https://www.textlocal.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-textlocal
```

The service provider auto-registers a `textlocal` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=textlocal
SMS_GATEWAY_TEXTLOCAL_API_KEY=your-api-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-textlocal-config
# or
php artisan sms-gateway-textlocal:install
```

## Usage

With `SMS_GATEWAY_DRIVER=textlocal`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'numbers' => '447123456789',
    'sender' => 'Laravel',
    'message' => 'Hello from Textlocal',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('textlocal')->send($data);
```

`send()` posts to `POST send/`, form-encoded. The payload goes straight to Textlocal, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Textlocal endpoint:

```php
$response = SmsGateway::driver('textlocal')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `textlocal` and the HTTP request and response.

## Configuration

`config/sms-gateway-textlocal.php`:

- `api_key` — your Textlocal API key (`SMS_GATEWAY_TEXTLOCAL_API_KEY`), sent as the `apikey` query parameter
- `base_url` — the endpoint (`SMS_GATEWAY_TEXTLOCAL_BASE_URL`), defaulting to `https://api.txtlocal.com/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-textlocal`.

## License

MIT. See [LICENSE](LICENSE).
