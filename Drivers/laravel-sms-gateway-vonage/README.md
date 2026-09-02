# Laravel SMS Gateway — Vonage Driver

A [Vonage](https://www.vonage.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-vonage
php artisan sms-gateway-vonage:install   # or: vendor:publish --tag=sms-gateway-vonage-config
```

The service provider auto-registers a `vonage` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=vonage
SMS_GATEWAY_VONAGE_API_KEY=your-api-key
SMS_GATEWAY_VONAGE_API_SECRET=your-api-secret
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'from' => 'Laravel',
    'to' => '14155550100',
    'text' => 'Hello from Vonage',
]);

SmsGateway::driver('vonage')->send($data);                     // regardless of the default
SmsGateway::driver('vonage')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST sms/json`, form-encoded. The payload goes straight to Vonage, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `vonage` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-vonage.php`:

| Key | Env (`SMS_GATEWAY_VONAGE_…`) | Default |
| --- | --- | --- |
| `api_key`, `api_secret` | `API_KEY`, `API_SECRET` | — |
| `base_url` | `BASE_URL` | `https://rest.nexmo.com/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as the `api_key` and `api_secret` query parameters. The
credentials and `base_url` are required and may not be empty: a missing or empty
value fails when the driver is resolved. Only connection failures and 5xx
responses are retried. Timeouts and the retry policy belong to this driver
alone, so tuning it leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-vonage`.

## License

MIT. See [LICENSE](LICENSE).
