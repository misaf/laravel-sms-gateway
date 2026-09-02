# Laravel SMS Gateway — MessageBird Driver

A [MessageBird](https://messagebird.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-messagebird
php artisan sms-gateway-messagebird:install   # or: vendor:publish --tag=sms-gateway-messagebird-config
```

The service provider auto-registers a `messagebird` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=messagebird
SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY=your-access-key
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'originator' => 'Laravel',
    'recipients' => ['31612345678'],
    'body' => 'Hello from MessageBird',
]);

SmsGateway::driver('messagebird')->send($data);                     // regardless of the default
SmsGateway::driver('messagebird')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST messages`, form-encoded. The payload goes straight to MessageBird, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `messagebird` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-messagebird.php`:

| Key | Env (`SMS_GATEWAY_MESSAGEBIRD_…`) | Default |
| --- | --- | --- |
| `access_key` | `ACCESS_KEY` | — |
| `base_url` | `BASE_URL` | `https://rest.messagebird.com/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

The access key is sent as the `Authorization: AccessKey {key}` header. The
credentials and `base_url` are required and may not be empty: a missing or empty
value fails when the driver is resolved. Only connection failures and 5xx
responses are retried. Timeouts and the retry policy belong to this driver
alone, so tuning it leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-messagebird`.

## License

MIT. See [LICENSE](LICENSE).
