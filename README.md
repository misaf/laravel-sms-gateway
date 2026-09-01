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
- `defaults.timeout` — the shared request timeout in seconds
  (`SMS_GATEWAY_TIMEOUT`), defaulting to `10`.
- `defaults.connect_timeout` — the shared connection timeout in seconds
  (`SMS_GATEWAY_CONNECT_TIMEOUT`), defaulting to `5`.

```env
SMS_GATEWAY_TIMEOUT=10
SMS_GATEWAY_CONNECT_TIMEOUT=5
```

That is the whole core configuration. Endpoints and credentials belong to the
driver packages.

## Registering a custom driver

Implement the `SmsGateway` contract. Each driver is self-contained: it owns its
base URL, its authentication, and the `SmsSent` dispatch.

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class CustomDriver implements SmsGateway
{
    private const DEFAULT_BASE_URL = 'https://api.example.com';

    public function __construct(
        private readonly string $token = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
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
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withToken($this->token)
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('custom', $request, $response);

                return $response;
            });
    }
}
```

Register it from a service provider:

```php
use Illuminate\Support\Facades\Config;

SmsGateway::extend('custom', fn (): SmsGateway => new CustomDriver(
    token: Config::string('services.custom.token'),
    timeout: Config::integer('sms-gateway.defaults.timeout'),
    connectTimeout: Config::integer('sms-gateway.defaults.connect_timeout'),
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
