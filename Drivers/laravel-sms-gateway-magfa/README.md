# Laravel SMS Gateway — Magfa Driver

A [Magfa](https://magfa.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-magfa
php artisan sms-gateway-magfa:install   # or: vendor:publish --tag=sms-gateway-magfa-config
```

The service provider auto-registers a `magfa` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=magfa
SMS_GATEWAY_MAGFA_USERNAME=your-username
SMS_GATEWAY_MAGFA_PASSWORD=your-password
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'senders' => ['3000'],
    'recipients' => ['09123456789'],
    'messages' => ['Hello from Magfa'],
]);

SmsGateway::driver('magfa')->send($data);                     // regardless of the default
SmsGateway::driver('magfa')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST send`, JSON. The payload goes straight to Magfa, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `magfa` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-magfa.php`:

| Key | Env (`SMS_GATEWAY_MAGFA_…`) | Default |
| --- | --- | --- |
| `username`, `password` | `USERNAME`, `PASSWORD` | — |
| `base_url` | `BASE_URL` | `https://sms.magfa.com/api/http/sms/v2/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as HTTP Basic authentication. The credentials and
`base_url` are required and may not be empty: a missing or empty value fails
when the driver is resolved. Only connection failures and 5xx responses are
retried. Timeouts and the retry policy belong to this driver alone, so tuning it
leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-magfa`.

## License

MIT. See [LICENSE](LICENSE).
