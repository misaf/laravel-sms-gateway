# Laravel SMS Gateway — SMS.ir Driver

A [SMS.ir](https://sms.ir) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-smsir
```

The service provider auto-registers a `smsir` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=smsir
SMS_GATEWAY_SMSIR_API_KEY=your-api-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-smsir-config
# or
php artisan sms-gateway-smsir:install
```

## Usage

With `SMS_GATEWAY_DRIVER=smsir`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'mobile' => '09123456789',
    'message' => 'Hello from SMS.ir',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('smsir')->send($data);
```

`send()` posts to `POST send/bulk`, JSON. The payload goes straight to SMS.ir, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other SMS.ir endpoint:

```php
$response = SmsGateway::driver('smsir')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `smsir` and the HTTP request and response.

## Configuration

`config/sms-gateway-smsir.php`:

- `api_key` — your SMS.ir API key (`SMS_GATEWAY_SMSIR_API_KEY`), sent as the `X-API-KEY` header; leave it empty in local and testing environments and no header is sent
- `base_url` — the endpoint (`SMS_GATEWAY_SMSIR_BASE_URL`), defaulting to `https://api.sms.ir/v1/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-smsir`.

## License

MIT. See [LICENSE](LICENSE).
