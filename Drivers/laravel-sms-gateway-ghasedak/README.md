# Laravel SMS Gateway — Ghasedak Driver

A [Ghasedak](https://ghasedak.me) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Requirements

PHP 8.4+, Laravel 13, `misaf/laravel-sms-gateway`.

## Installation

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

The service provider auto-registers a `ghasedak` driver on the core manager. Point
the core package at it:

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-ghasedak-config
# or
php artisan sms-gateway-ghasedak:install
```

## Usage

With `SMS_GATEWAY_DRIVER=ghasedak`, the core facade uses this driver with no
further changes:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Here is a test message.',
    'receptor' => '+989119632587',
]);
```

To use it for a single call regardless of the default, name it:

```php
$response = SmsGateway::driver('ghasedak')->send($data);
```

`send()` posts to `POST sms/send/simple`. The payload goes straight to Ghasedak, so use
the fields its API expects.

Reach the configured Laravel HTTP client directly with `request()` to call any
other Ghasedak endpoint:

```php
$response = SmsGateway::driver('ghasedak')->request()->get('some/endpoint');
```

Every send dispatches the core events — `SmsSending`, then `SmsSent` on a
successful response or `SmsSendFailed` on a failed one — with the driver name
`ghasedak`. See the core package README for their payloads.

## Configuration

`config/sms-gateway-ghasedak.php`:

- `api_key` — your Ghasedak API key (`SMS_GATEWAY_GHASEDAK_API_KEY`), sent as the `apikey` header; required — a missing environment variable fails when the driver is resolved
- `base_url` — the endpoint (`SMS_GATEWAY_GHASEDAK_BASE_URL`), defaulting to `https://api.ghasedak.me/v2/`; optional, leave it empty to use that default
- `timeout.server` — the connection timeout in seconds (`SMS_GATEWAY_GHASEDAK_SERVER_TIMEOUT`), defaulting to the core `SMS_GATEWAY_SERVER_TIMEOUT`, then to `5`
- `timeout.client` — the request timeout in seconds (`SMS_GATEWAY_GHASEDAK_CLIENT_TIMEOUT`), defaulting to the core `SMS_GATEWAY_CLIENT_TIMEOUT`, then to `6`; keep it above the connection timeout
- `retry.times` — how many attempts a send gets (`SMS_GATEWAY_GHASEDAK_RETRY_TIMES`), defaulting to the core `SMS_GATEWAY_RETRY_TIMES`, then to `2`
- `retry.sleep_milliseconds` — the pause between attempts (`SMS_GATEWAY_GHASEDAK_RETRY_SLEEP_MILLISECONDS`), defaulting to the core `SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS`, then to `100`

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt. Leave the
driver-specific timeout and retry variables unset to follow the shared defaults
in `config/sms-gateway.php`.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the
monorepo, where this driver lives at `Drivers/laravel-sms-gateway-ghasedak`.

## License

MIT. See [LICENSE](LICENSE).
