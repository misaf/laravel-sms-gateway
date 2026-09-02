# Laravel SMS Gateway — Melipayamak Driver

A [Melipayamak](https://www.payamak-panel.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-melipayamak
php artisan sms-gateway-melipayamak:install   # or: vendor:publish --tag=sms-gateway-melipayamak-config
```

The service provider auto-registers a `melipayamak` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=melipayamak
SMS_GATEWAY_MELIPAYAMAK_USERNAME=your-username
SMS_GATEWAY_MELIPAYAMAK_PASSWORD=your-password
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'to' => '09123456789',
    'from' => '50004000',
    'text' => 'Hello from Melipayamak',
]);

SmsGateway::driver('melipayamak')->send($data);                     // regardless of the default
SmsGateway::driver('melipayamak')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST SendSMS/SendSMS`, form-encoded. The payload goes straight to Melipayamak, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `melipayamak` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-melipayamak.php`:

| Key | Env (`SMS_GATEWAY_MELIPAYAMAK_…`) | Default |
| --- | --- | --- |
| `username`, `password` | `USERNAME`, `PASSWORD` | — |
| `base_url` | `BASE_URL` | `https://rest.payamak-panel.com/api/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as the `username` and `password` query parameters. The
credentials and `base_url` are required and may not be empty: a missing or empty
value fails when the driver is resolved. Only connection failures and 5xx
responses are retried. Timeouts and the retry policy belong to this driver
alone, so tuning it leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-melipayamak`.

## License

MIT. See [LICENSE](LICENSE).
