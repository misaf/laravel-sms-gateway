# Laravel SMS Gateway — Kavenegar Driver

A [Kavenegar](https://kavenegar.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-kavenegar
php artisan sms-gateway-kavenegar:install   # or: vendor:publish --tag=sms-gateway-kavenegar-config
```

The service provider auto-registers a `kavenegar` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=kavenegar
SMS_GATEWAY_KAVENEGAR_API_KEY=your-api-key
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'receptor' => '09123456789',
    'message' => 'Hello from Kavenegar',
]);

SmsGateway::driver('kavenegar')->send($data);                     // regardless of the default
SmsGateway::driver('kavenegar')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST sms/send.json`, form-encoded. The payload goes straight to Kavenegar, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `kavenegar` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-kavenegar.php`:

| Key | Env (`SMS_GATEWAY_KAVENEGAR_…`) | Default |
| --- | --- | --- |
| `api_key` | `API_KEY` | — |
| `base_url` | `BASE_URL` | `https://api.kavenegar.com/v1/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

The API key is appended to the base URL to scope requests to your account. The
credentials and `base_url` are required and may not be empty: a missing or empty
value fails when the driver is resolved. Only connection failures and 5xx
responses are retried. Timeouts and the retry policy belong to this driver
alone, so tuning it leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-kavenegar`.

## License

MIT. See [LICENSE](LICENSE).
