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

Every send dispatches the core events — `SmsSending`, then `SmsSent` on a
successful response, `SmsSendFailed` on a failed one, or `SmsSendUnreachable`
when the gateway was never reached — with the driver name `messagebird`. See the
core package README for their payloads.

## Configuration

`config/sms-gateway-messagebird.php`:

- `access_key` — your MessageBird access key (`SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY`), sent as the `Authorization: AccessKey {key}` header; required — a missing or empty environment variable fails when the driver is resolved
- `base_url` — the endpoint (`SMS_GATEWAY_MESSAGEBIRD_BASE_URL`), defaulting to `https://rest.messagebird.com/`; required and may not be empty — it is the single source of truth for the endpoint, so point it at a proxy or a sandbox by editing it here
- `timeout.server` — the connection timeout in seconds (`SMS_GATEWAY_MESSAGEBIRD_SERVER_TIMEOUT`), defaulting to `5`
- `timeout.client` — the request timeout in seconds (`SMS_GATEWAY_MESSAGEBIRD_CLIENT_TIMEOUT`), defaulting to `6`; keep it above the connection timeout
- `retry.times` — how many attempts a send gets (`SMS_GATEWAY_MESSAGEBIRD_RETRY_TIMES`), defaulting to `2`
- `retry.sleep_milliseconds` — the pause between attempts (`SMS_GATEWAY_MESSAGEBIRD_RETRY_SLEEP_MILLISECONDS`), defaulting to `100`

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt. Timeouts and the
retry policy belong to this driver alone, so tuning it leaves the other
gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-messagebird`.

## License

MIT. See [LICENSE](LICENSE).
