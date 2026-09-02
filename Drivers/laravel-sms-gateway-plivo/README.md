# Laravel SMS Gateway — Plivo Driver

A [Plivo](https://plivo.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-plivo
php artisan sms-gateway-plivo:install   # or: vendor:publish --tag=sms-gateway-plivo-config
```

The service provider auto-registers a `plivo` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=plivo
SMS_GATEWAY_PLIVO_AUTH_ID=your-auth-id
SMS_GATEWAY_PLIVO_AUTH_TOKEN=your-auth-token
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'src' => '14151234567',
    'dst' => '14157654321',
    'text' => 'Hello from Plivo',
]);

SmsGateway::driver('plivo')->send($data);                     // regardless of the default
SmsGateway::driver('plivo')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST Message/`, JSON. The payload goes straight to Plivo, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `plivo` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-plivo.php`:

| Key | Env (`SMS_GATEWAY_PLIVO_…`) | Default |
| --- | --- | --- |
| `auth_id`, `auth_token` | `AUTH_ID`, `AUTH_TOKEN` | — |
| `base_url` | `BASE_URL` | `https://api.plivo.com/v1/Account/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as HTTP Basic authentication, and the auth id is appended
to the base URL to scope requests to your account. The credentials and
`base_url` are required and may not be empty: a missing or empty value fails
when the driver is resolved. Only connection failures and 5xx responses are
retried. Timeouts and the retry policy belong to this driver alone, so tuning it
leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-plivo`.

## License

MIT. See [LICENSE](LICENSE).
