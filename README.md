# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel, with one installable package per
provider. Payloads pass through untouched, so every provider's own API fields
stay available, and every send attempt, success and failure dispatches an event.

Requires PHP 8.4+ and Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway
php artisan sms-gateway:install   # or: vendor:publish --tag=sms-gateway-config
```

The core ships only the `null` driver, which sends nothing and returns a fake
successful response — fine for local and testing, but it delivers no messages.
For that, install one or more driver packages:

| Package | Driver | Provider |
| --- | --- | --- |
| [`misaf/laravel-sms-gateway-ghasedak`](https://github.com/misaf/laravel-sms-gateway-ghasedak) | `ghasedak` | [Ghasedak](https://ghasedak.me) |
| [`misaf/laravel-sms-gateway-ippanel`](https://github.com/misaf/laravel-sms-gateway-ippanel) | `ippanel` | [IPPanel](https://ippanel.com) |
| [`misaf/laravel-sms-gateway-kavenegar`](https://github.com/misaf/laravel-sms-gateway-kavenegar) | `kavenegar` | [Kavenegar](https://kavenegar.com) |
| [`misaf/laravel-sms-gateway-magfa`](https://github.com/misaf/laravel-sms-gateway-magfa) | `magfa` | [Magfa](https://magfa.com) |
| [`misaf/laravel-sms-gateway-melipayamak`](https://github.com/misaf/laravel-sms-gateway-melipayamak) | `melipayamak` | [Melipayamak](https://www.payamak-panel.com) |
| [`misaf/laravel-sms-gateway-messagebird`](https://github.com/misaf/laravel-sms-gateway-messagebird) | `messagebird` | [MessageBird](https://messagebird.com) |
| [`misaf/laravel-sms-gateway-plivo`](https://github.com/misaf/laravel-sms-gateway-plivo) | `plivo` | [Plivo](https://plivo.com) |
| [`misaf/laravel-sms-gateway-smsir`](https://github.com/misaf/laravel-sms-gateway-smsir) | `smsir` | [SMS.ir](https://sms.ir) |
| [`misaf/laravel-sms-gateway-sunway`](https://github.com/misaf/laravel-sms-gateway-sunway) | `sunway` | [Sunway](https://www.sunwaysms.com) |
| [`misaf/laravel-sms-gateway-textlocal`](https://github.com/misaf/laravel-sms-gateway-textlocal) | `textlocal` | [Textlocal](https://www.textlocal.com) |
| [`misaf/laravel-sms-gateway-twilio`](https://github.com/misaf/laravel-sms-gateway-twilio) | `twilio` | [Twilio](https://twilio.com) |
| [`misaf/laravel-sms-gateway-vonage`](https://github.com/misaf/laravel-sms-gateway-vonage) | `vonage` | [Vonage](https://www.vonage.com) |

All service providers auto-register, and each driver registers itself on the
core manager. See each driver's README for its credentials and options.

## Usage

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Hello',
    'receptor' => '09123456789',
]);

SmsGateway::driver('kavenegar')->send($data);                              // a named driver
SmsGateway::driver('ghasedak')->request()->post('sms/send/simple', $data); // the raw HTTP client
```

Drivers can coexist, and the payload goes straight to the provider, so use the
fields its API expects. The facade is the entry point: the container binds only
`SmsGatewayManager` and its `sms-gateway` alias — the `SmsGateway` contract is
deliberately not bound, so type-hinting it for injection will not resolve.

### Events

Every HTTP driver dispatches four events from `Misaf\LaravelSmsGateway\Events`,
each carrying the driver name:

- `SmsSending` — before the request leaves, with the payload
- `SmsSent` — after a successful (2xx) response, with the `Request` and `Response`
- `SmsSendFailed` — after a failed (non-2xx) response, with the `Request` and `Response`
- `SmsSendUnreachable` — when the gateway was never reached (connection error or
  timeout), with the `exception`; there is no request or response to report

```php
use Misaf\LaravelSmsGateway\Events\SmsSendFailed;

final class ReportSmsGatewayFailure
{
    public function handle(SmsSendFailed $event): void
    {
        logger()->error($event->driverName, [
            'status' => $event->response->status(),
            'body' => $event->response->body(),
        ]);
    }
}
```

The retry policy uses `throw: false`, so a rejected send does not raise —
`SmsSendFailed` is how you observe it. A connection error or timeout still
raises after the retries are spent, with `SmsSendUnreachable` dispatched just
before it surfaces.

## Configuration

`config/sms-gateway.php`:

| Key | Env | Default |
| --- | --- | --- |
| `default` | `SMS_GATEWAY_DRIVER` | `null` |
| `defaults.server_timeout` | `SMS_GATEWAY_SERVER_TIMEOUT` | `5` |
| `defaults.client_timeout` | `SMS_GATEWAY_CLIENT_TIMEOUT` | `6` |
| `defaults.retry_times` | `SMS_GATEWAY_RETRY_TIMES` | `2` |
| `defaults.retry_sleep_milliseconds` | `SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS` | `100` |

The client timeout sits one second above the connection timeout so a slow
gateway loses the race. Only connection failures and gateway 5xx responses are
retried; a rejected credential or a malformed payload fails on the first attempt.

That is the whole core configuration. The `defaults.*` values are the fallback
for custom drivers only — each first-party driver owns its own `timeout.*` and
`retry.*` keys with driver-specific environment variables (e.g.
`SMS_GATEWAY_TWILIO_SERVER_TIMEOUT`), so one gateway can be tuned without
touching the others. Endpoints and credentials belong to the driver packages,
where every credential key and every `base_url` is required and may not be empty:
a missing or empty value fails when the driver is resolved, rather than sending
an unauthenticated request or one to a relative URL.

## Registering a custom driver

Extend `Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver`. The base class owns
the timeouts, the retry policy and the events; the driver supplies its name, its
authentication, and the call it makes. Constructor values take no defaults —
config is the only place a value is written down — and every one the driver
cannot work without is guarded with `self::requireConfigured()`, since a config
key that is present but empty passes `Config::string()`.

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class CustomDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $token,
        int $serverTimeout,
        int $clientTimeout,
        int $retryTimes,
        int $retrySleepMilliseconds,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function driverName(): string
    {
        return 'custom';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withToken($this->token);
    }
}
```

A driver that needs neither the shared retry policy nor the events may implement
`Misaf\LaravelSmsGateway\Contracts\SmsGateway` directly instead.

Register it from a service provider:

```php
use Illuminate\Support\Facades\Config;

SmsGateway::extend('custom', fn (): SmsGateway => new CustomDriver(
    baseUrl: Config::string('services.custom.base_url'),
    token: Config::string('services.custom.token'),
    serverTimeout: Config::integer('sms-gateway.defaults.server_timeout'),
    clientTimeout: Config::integer('sms-gateway.defaults.client_timeout'),
    retryTimes: Config::integer('sms-gateway.defaults.retry_times'),
    retrySleepMilliseconds: Config::integer('sms-gateway.defaults.retry_sleep_milliseconds'),
));
```

From a *package* service provider, defer the registration so provider discovery
order cannot matter:

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

$this->callAfterResolving(
    SmsGatewayManager::class,
    fn (SmsGatewayManager $manager) => $manager->extend('custom', $factory),
);
```

The registration key is the name used by `SmsGateway::driver('custom')`; the
driver reads its own configuration and reports its own `driverName()` on the
events, so nothing is inferred from that key.

## Contributing

This repository is a monorepo: the core package lives at the root, and every
driver lives in `Drivers/laravel-sms-gateway-<driver>` and is split out to its
own read-only repository on release. Open issues and pull requests here.

```bash
composer test       # Pest
composer analyse    # PHPStan / Larastan
composer format     # Pint
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for what has changed recently.

## License

MIT. See [LICENSE](LICENSE).
