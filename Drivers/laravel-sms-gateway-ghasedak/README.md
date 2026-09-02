# Laravel SMS Gateway — Ghasedak Driver

A [Ghasedak](https://ghasedak.me) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-ghasedak
php artisan sms-gateway-ghasedak:install   # or: vendor:publish --tag=sms-gateway-ghasedak-config
```

The service provider auto-registers a `ghasedak` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Hello from Ghasedak',
    'receptor' => '09123456789',
]);

SmsGateway::driver('ghasedak')->send($data);                     // regardless of the default
SmsGateway::driver('ghasedak')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST sms/send/simple`. The payload goes straight to Ghasedak, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `ghasedak` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-ghasedak.php`:

| Key | Env (`SMS_GATEWAY_GHASEDAK_…`) | Default |
| --- | --- | --- |
| `api_key` | `API_KEY` | — |
| `base_url` | `BASE_URL` | `https://api.ghasedak.me/v2/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

The API key is sent as the `apikey` header. The credentials and `base_url` are
required and may not be empty: a missing or empty value fails when the driver is
resolved. Only connection failures and 5xx responses are retried. Timeouts and
the retry policy belong to this driver alone, so tuning it leaves the other
gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-ghasedak`.

## License

MIT. See [LICENSE](LICENSE).
