# Laravel SMS Gateway — Sunway Driver

A [Sunway](https://www.sunwaysms.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-sunway
php artisan sms-gateway-sunway:install   # or: vendor:publish --tag=sms-gateway-sunway-config
```

The service provider auto-registers a `sunway` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=sunway
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'method' => 'SendSMS',
    'mobile' => '09123456789',
    'message' => 'Hello from Sunway',
]);

SmsGateway::driver('sunway')->send($data);                     // regardless of the default
SmsGateway::driver('sunway')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `GET HttpService.ashx`, with the payload as query parameters. The payload goes straight to Sunway, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `sunway` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-sunway.php`:

| Key | Env (`SMS_GATEWAY_SUNWAY_…`) | Default |
| --- | --- | --- |
| `username`, `password` | `USERNAME`, `PASSWORD` | — |
| `base_url` | `BASE_URL` | `https://sms.sunwaysms.com/smsws/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as the `UserName` and `Password` query parameters. The
credentials and `base_url` are required and may not be empty: a missing or empty
value fails when the driver is resolved. Only connection failures and 5xx
responses are retried. Timeouts and the retry policy belong to this driver
alone, so tuning it leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-sunway`.

## License

MIT. See [LICENSE](LICENSE).
