# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel.

- Driver-based sending through a Laravel `Manager`, with one installable package per provider
- Payloads pass through untouched, so every provider's own API fields stay available
- Direct access to each driver's configured HTTP client, plus an `SmsSent` event on every request

## Requirements

PHP 8.4+, Laravel 13.

## Installation

```bash
composer require misaf/laravel-sms-gateway
```

This gives you the `null` driver, which sends nothing and returns a successful
fake response — fine for local and testing, but it delivers no messages. For
that, install one or more driver packages:

| Package | Driver | Provider |
| --- | --- | --- |
| *(core)* | `null` | none — never sends |
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

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

All service providers are auto-registered, and each driver registers itself on
the core manager. See each driver's README for its credentials and options.

Publish the config:

```bash
php artisan vendor:publish --tag=sms-gateway-config
# or
php artisan sms-gateway:install
```

## Usage

Set the default driver and its credentials in `.env`:

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key
```

Send a message:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Hello',
    'receptor' => '09123456789',
]);
```

The payload goes straight to the provider, so use the fields its API expects.

Drivers can coexist; name one to send through it regardless of the default:

```php
SmsGateway::driver('kavenegar')->send($data);
```

The facade is the entry point. The container binds only `SmsGatewayManager` and
its `sms-gateway` alias — the `SmsGateway` contract is not bound, so
type-hinting it for injection will not resolve; resolve a driver through the
facade or the manager instead.

### HTTP client access

Use `request()` to reach a driver's configured Laravel HTTP client directly
instead of `send()`:

```php
$response = SmsGateway::driver('ghasedak')
    ->request()
    ->post('sms/send/simple', $data);
```

### Events

Every HTTP driver dispatches `Misaf\LaravelSmsGateway\Events\SmsSent` with the
driver name and the Laravel HTTP `Request` and `Response`:

```php
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class StoreSmsGatewayResult
{
    public function handle(SmsSent $event): void
    {
        logger($event->driverName, $event->response->json());
    }
}
```

## Configuration

`config/sms-gateway.php`:

- `default` — the driver name (`SMS_GATEWAY_DRIVER`), falling back to `null`.
- `defaults.server_timeout` — the shared connection timeout in seconds
  (`SMS_GATEWAY_SERVER_TIMEOUT`), defaulting to `5`.
- `defaults.client_timeout` — the shared request timeout in seconds
  (`SMS_GATEWAY_CLIENT_TIMEOUT`), defaulting to `6`, one second above the
  connection timeout so a slow gateway loses the race.
- `defaults.retry_times` — how many attempts a request gets
  (`SMS_GATEWAY_RETRY_TIMES`), defaulting to `2`.
- `defaults.retry_sleep_milliseconds` — the pause between attempts
  (`SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS`), defaulting to `100`.

Only connection failures and gateway 5xx responses are retried; a rejected
credential or a malformed payload fails on the first attempt.

```env
SMS_GATEWAY_SERVER_TIMEOUT=5
SMS_GATEWAY_CLIENT_TIMEOUT=6
SMS_GATEWAY_RETRY_TIMES=2
SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS=100
```

That is the whole core configuration. Endpoints and credentials belong to the
driver packages.

## Registering a custom driver

Implement the `SmsGateway` contract. Each driver is self-contained: it owns its
base URL, its authentication, and the `SmsSent` dispatch.

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Throwable;

final class CustomDriver implements SmsGateway
{
    private const DEFAULT_BASE_URL = 'https://api.example.com';

    public function __construct(
        private readonly string $token = '',
        private readonly string $baseUrl = '',
        private readonly int $serverTimeout = 5,
        private readonly int $clientTimeout = 6,
        private readonly int $retryTimes = 2,
        private readonly int $retrySleepMilliseconds = 100,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL)
            ->connectTimeout($this->serverTimeout)
            ->timeout($this->clientTimeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleepMilliseconds,
                $this->shouldRetry(...),
                throw: false,
            )
            ->withToken($this->token)
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('custom', $request, $response);

                return $response;
            });
    }

    private function shouldRetry(Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        return $exception instanceof RequestException
            && $exception->response->serverError();
    }
}
```

Register it from a service provider:

```php
use Illuminate\Support\Facades\Config;

SmsGateway::extend('custom', fn (): SmsGateway => new CustomDriver(
    token: Config::string('services.custom.token'),
    serverTimeout: Config::integer('sms-gateway.defaults.server_timeout'),
    clientTimeout: Config::integer('sms-gateway.defaults.client_timeout'),
    retryTimes: Config::integer('sms-gateway.defaults.retry_times'),
    retrySleepMilliseconds: Config::integer('sms-gateway.defaults.retry_sleep_milliseconds'),
));
```

From a package service provider, defer the registration so provider discovery
order cannot matter:

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

$this->callAfterResolving(
    SmsGatewayManager::class,
    fn (SmsGatewayManager $manager) => $manager->extend(
        'custom',
        fn (): SmsGateway => new CustomDriver(),
    ),
);
```

The registration key is the name used by `SmsGateway::driver('custom')`. The
driver reads its own configuration and passes the name it dispatches `SmsSent`
with, so nothing is inferred from the registration key.

## Contributing

This repository is a monorepo: the core package lives at the root, and every
driver lives in `Drivers/laravel-sms-gateway-<driver>` and is split out to its
own read-only repository on release. Open issues and pull requests here.

## Testing

```bash
composer test       # Pest
composer analyse    # PHPStan / Larastan
composer format     # Pint
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed
recently.

## License

MIT. See [LICENSE](LICENSE).
