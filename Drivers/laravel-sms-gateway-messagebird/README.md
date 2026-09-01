# Laravel SMS Gateway — MessageBird Driver

A [MessageBird](https://messagebird.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-messagebird
```

The service provider auto-registers a `messagebird` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=messagebird
SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY=your-access-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-messagebird-config
# or
php artisan sms-gateway-messagebird:install
```

## Usage

With `SMS_GATEWAY_DRIVER=messagebird`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'originator' => 'Laravel',
    'recipients' => ['31612345678'],
    'body' => 'Hello from MessageBird',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('messagebird')->send($data);
```

`send()` posts to `POST messages`, form-encoded. The payload goes straight to MessageBird, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other MessageBird endpoint:

```php
$response = SmsGateway::driver('messagebird')->request()->get('some/endpoint');
```

Every request dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name `messagebird` and the HTTP request and response.

## Configuration

`config/sms-gateway-messagebird.php`:

- `access_key` — your MessageBird access key (`SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY`), sent as the `Authorization: AccessKey {key}` header
- `base_url` — the endpoint (`SMS_GATEWAY_MESSAGEBIRD_BASE_URL`), defaulting to `https://rest.messagebird.com/`

Timeouts are shared with the core package — `SMS_GATEWAY_TIMEOUT` and
`SMS_GATEWAY_CONNECT_TIMEOUT` from `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-messagebird`.

## License

MIT. See [LICENSE](LICENSE).
