# Laravel SMS Gateway — Melipayamak Driver

A [Melipayamak](https://www.payamak-panel.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-melipayamak
```

The service provider auto-registers a `melipayamak` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=melipayamak
SMS_GATEWAY_MELIPAYAMAK_USERNAME=your-username
SMS_GATEWAY_MELIPAYAMAK_PASSWORD=your-password
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-melipayamak-config
# or
php artisan sms-gateway-melipayamak:install
```

## Usage

With `SMS_GATEWAY_DRIVER=melipayamak`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'to' => '09123456789',
    'from' => '50004000',
    'text' => 'Hello from Melipayamak',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('melipayamak')->send($data);
```

`send()` posts to `POST SendSMS/SendSMS`, form-encoded. The payload goes straight to Melipayamak, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Melipayamak endpoint:

```php
$response = SmsGateway::driver('melipayamak')->request()->get('some/endpoint');
```

Every send dispatches the core events — `SmsSending`, then `SmsSent` on a
successful response or `SmsSendFailed` on a failed one — with the driver name
`melipayamak`. See the core package README for their payloads.

## Configuration

`config/sms-gateway-melipayamak.php`:

- `username` / `password` — your Melipayamak (Payamak-panel) credentials (`SMS_GATEWAY_MELIPAYAMAK_USERNAME`, `SMS_GATEWAY_MELIPAYAMAK_PASSWORD`), sent as the `username` and `password` query parameters; both are required — a missing environment variable fails when the driver is resolved
- `base_url` — the endpoint (`SMS_GATEWAY_MELIPAYAMAK_BASE_URL`), defaulting to `https://rest.payamak-panel.com/api/`; optional, leave it empty to use that default
- `timeout.server` — the connection timeout in seconds (`SMS_GATEWAY_MELIPAYAMAK_SERVER_TIMEOUT`), defaulting to the core `SMS_GATEWAY_SERVER_TIMEOUT`, then to `5`
- `timeout.client` — the request timeout in seconds (`SMS_GATEWAY_MELIPAYAMAK_CLIENT_TIMEOUT`), defaulting to the core `SMS_GATEWAY_CLIENT_TIMEOUT`, then to `6`; keep it above the connection timeout
- `retry.times` — how many attempts a send gets (`SMS_GATEWAY_MELIPAYAMAK_RETRY_TIMES`), defaulting to the core `SMS_GATEWAY_RETRY_TIMES`, then to `2`
- `retry.sleep_milliseconds` — the pause between attempts (`SMS_GATEWAY_MELIPAYAMAK_RETRY_SLEEP_MILLISECONDS`), defaulting to the core `SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS`, then to `100`

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt. Leave the
driver-specific timeout and retry variables unset to follow the shared defaults
in `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-melipayamak`.

## License

MIT. See [LICENSE](LICENSE).
