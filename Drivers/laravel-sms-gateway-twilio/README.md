# Laravel SMS Gateway — Twilio Driver

A [Twilio](https://twilio.com) SMS driver for
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).
Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway-twilio
php artisan sms-gateway-twilio:install   # or: vendor:publish --tag=sms-gateway-twilio-config
```

The service provider auto-registers a `twilio` driver on the core manager:

```env
SMS_GATEWAY_DRIVER=twilio
SMS_GATEWAY_TWILIO_ACCOUNT_SID=your-account-sid
SMS_GATEWAY_TWILIO_AUTH_TOKEN=your-auth-token
```

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'To' => '+15005550006',
    'From' => '+15005550001',
    'Body' => 'Hello from Twilio',
]);

SmsGateway::driver('twilio')->send($data);                     // regardless of the default
SmsGateway::driver('twilio')->request()->get('some/endpoint'); // any other endpoint
```

`send()` posts to `POST Messages.json`, form-encoded. The payload goes straight to Twilio, so use
the fields its API expects. Every send dispatches the core `SmsSending`, `SmsSent`,
`SmsSendFailed` and `SmsSendUnreachable` events with the driver name `twilio` — see
the [core README](https://github.com/misaf/laravel-sms-gateway#events).

## Configuration

`config/sms-gateway-twilio.php`:

| Key | Env (`SMS_GATEWAY_TWILIO_…`) | Default |
| --- | --- | --- |
| `account_sid`, `auth_token` | `ACCOUNT_SID`, `AUTH_TOKEN` | — |
| `base_url` | `BASE_URL` | `https://api.twilio.com/2010-04-01/Accounts/` |
| `timeout.server` | `SERVER_TIMEOUT` | `5` |
| `timeout.client` | `CLIENT_TIMEOUT` | `6` |
| `retry.times` | `RETRY_TIMES` | `2` |
| `retry.sleep_milliseconds` | `RETRY_SLEEP_MILLISECONDS` | `100` |

Credentials are sent as HTTP Basic authentication, and the account sid is
appended to the base URL to scope requests to your account. The credentials and
`base_url` are required and may not be empty: a missing or empty value fails
when the driver is resolved. Only connection failures and 5xx responses are
retried. Timeouts and the retry policy belong to this driver alone, so tuning it
leaves the other gateways untouched.

## Contributing

This repository is a read-only split of the
[monorepo](https://github.com/misaf/laravel-sms-gateway); commits made here are
overwritten by the next split. Open issues and pull requests against the monorepo,
where this driver lives at `Drivers/laravel-sms-gateway-twilio`.

## License

MIT. See [LICENSE](LICENSE).
