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

Every send dispatches the core events — `SmsSending`, then `SmsSent` on a
successful response, `SmsSendFailed` on a failed one, or `SmsSendUnreachable`
when the gateway was never reached — with the driver name `kavenegar`. See the
core package README for their payloads.

## Configuration

`config/sms-gateway-kavenegar.php`:

- `api_key` — your Kavenegar API key (`SMS_GATEWAY_KAVENEGAR_API_KEY`); it is appended to the base URL to scope requests to your account; required — a missing or empty environment variable fails when the driver is resolved
- `base_url` — the endpoint (`SMS_GATEWAY_KAVENEGAR_BASE_URL`), defaulting to `https://api.kavenegar.com/v1/`, to which the driver appends the API key; required and may not be empty — it is the single source of truth for the endpoint, so point it at a proxy or a sandbox by editing it here
- `timeout.server` — the connection timeout in seconds (`SMS_GATEWAY_KAVENEGAR_SERVER_TIMEOUT`), defaulting to `5`
- `timeout.client` — the request timeout in seconds (`SMS_GATEWAY_KAVENEGAR_CLIENT_TIMEOUT`), defaulting to `6`; keep it above the connection timeout
- `retry.times` — how many attempts a send gets (`SMS_GATEWAY_KAVENEGAR_RETRY_TIMES`), defaulting to `2`
- `retry.sleep_milliseconds` — the pause between attempts (`SMS_GATEWAY_KAVENEGAR_RETRY_SLEEP_MILLISECONDS`), defaulting to `100`

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt. Timeouts and the
retry policy belong to this driver alone, so tuning it leaves the other
gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-kavenegar`.

## License

MIT. See [LICENSE](LICENSE).
