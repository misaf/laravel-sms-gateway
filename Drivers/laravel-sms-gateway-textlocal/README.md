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

Every send dispatches the core events — `SmsSending`, then `SmsSent` on a
successful response, `SmsSendFailed` on a failed one, or `SmsSendUnreachable`
when the gateway was never reached — with the driver name `textlocal`. See the
core package README for their payloads.

## Configuration

`config/sms-gateway-textlocal.php`:

- `api_key` — your Textlocal API key (`SMS_GATEWAY_TEXTLOCAL_API_KEY`), sent as the `apikey` query parameter; required — a missing or empty environment variable fails when the driver is resolved
- `base_url` — the endpoint (`SMS_GATEWAY_TEXTLOCAL_BASE_URL`), defaulting to `https://api.txtlocal.com/`; required and may not be empty — it is the single source of truth for the endpoint, so point it at a proxy or a sandbox by editing it here
- `timeout.server` — the connection timeout in seconds (`SMS_GATEWAY_TEXTLOCAL_SERVER_TIMEOUT`), defaulting to `5`
- `timeout.client` — the request timeout in seconds (`SMS_GATEWAY_TEXTLOCAL_CLIENT_TIMEOUT`), defaulting to `6`; keep it above the connection timeout
- `retry.times` — how many attempts a send gets (`SMS_GATEWAY_TEXTLOCAL_RETRY_TIMES`), defaulting to `2`
- `retry.sleep_milliseconds` — the pause between attempts (`SMS_GATEWAY_TEXTLOCAL_RETRY_SLEEP_MILLISECONDS`), defaulting to `100`

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt. Timeouts and the
retry policy belong to this driver alone, so tuning it leaves the other
gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-textlocal`.

## License

MIT. See [LICENSE](LICENSE).
