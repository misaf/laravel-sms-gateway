# Laravel SMS Gateway — Vonage Driver

A [Vonage](https://www.vonage.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-vonage
```

The service provider auto-registers a `vonage` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=vonage
SMS_GATEWAY_VONAGE_API_KEY=your-api-key
SMS_GATEWAY_VONAGE_API_SECRET=your-api-secret
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-vonage-config
# or
php artisan sms-gateway-vonage:install
```

## Usage

With `SMS_GATEWAY_DRIVER=vonage`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'from' => 'Laravel',
    'to' => '14155550100',
    'text' => 'Hello from Vonage',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('vonage')->send($data);
```

`send()` posts to `POST sms/json`, form-encoded. The payload goes straight to Vonage, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Vonage endpoint:

```php
$response = SmsGateway::driver('vonage')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `vonage` and the HTTP request and response.

## Configuration

`config/sms-gateway-vonage.php`:

- `api_key` / `api_secret` — your Vonage credentials (`SMS_GATEWAY_VONAGE_API_KEY`, `SMS_GATEWAY_VONAGE_API_SECRET`), sent as the `api_key` and `api_secret` query parameters
- `base_url` — the endpoint (`SMS_GATEWAY_VONAGE_BASE_URL`), defaulting to `https://rest.nexmo.com/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-vonage`.

## License

MIT. See [LICENSE](LICENSE).
