# Laravel SMS Gateway

A simple driver-based SMS gateway manager for Laravel.

## Features

- Separate, independently installable packages for each provider.
- Switch drivers per request.
- Laravel HTTP client access.
- SMS sent events with request and response data.
- Custom driver registration.

## Requirements

- PHP 8.4+
- Laravel 13+

## Installation

Install the core package:

```bash
composer require misaf/laravel-sms-gateway
```

Install one or more driver packages:

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

Publish the config file:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

Each driver package publishes its own config file the same way, for example
`--tag=sms-gateway-ghasedak-config`.

## Driver Packages

First-party driver packages:

| Driver | Package |
| --- | --- |
| `ghasedak` | [`misaf/laravel-sms-gateway-ghasedak`](https://github.com/misaf/laravel-sms-gateway-ghasedak) |
| `ippanel` | [`misaf/laravel-sms-gateway-ippanel`](https://github.com/misaf/laravel-sms-gateway-ippanel) |
| `kavenegar` | [`misaf/laravel-sms-gateway-kavenegar`](https://github.com/misaf/laravel-sms-gateway-kavenegar) |
| `magfa` | [`misaf/laravel-sms-gateway-magfa`](https://github.com/misaf/laravel-sms-gateway-magfa) |
| `melipayamak` | [`misaf/laravel-sms-gateway-melipayamak`](https://github.com/misaf/laravel-sms-gateway-melipayamak) |
| `messagebird` | [`misaf/laravel-sms-gateway-messagebird`](https://github.com/misaf/laravel-sms-gateway-messagebird) |
| `plivo` | [`misaf/laravel-sms-gateway-plivo`](https://github.com/misaf/laravel-sms-gateway-plivo) |
| `smsir` | [`misaf/laravel-sms-gateway-smsir`](https://github.com/misaf/laravel-sms-gateway-smsir) |
| `sunway` | [`misaf/laravel-sms-gateway-sunway`](https://github.com/misaf/laravel-sms-gateway-sunway) |
| `textlocal` | [`misaf/laravel-sms-gateway-textlocal`](https://github.com/misaf/laravel-sms-gateway-textlocal) |
| `twilio` | [`misaf/laravel-sms-gateway-twilio`](https://github.com/misaf/laravel-sms-gateway-twilio) |
| `vonage` | [`misaf/laravel-sms-gateway-vonage`](https://github.com/misaf/laravel-sms-gateway-vonage) |

## Quick Start

Install the Ghasedak driver package:

```bash
composer require misaf/laravel-sms-gateway-ghasedak
```

Set the default driver and its credentials in `.env`:

```env
SMS_GATEWAY_DRIVER=ghasedak # Default driver
SMS_GATEWAY_GHASEDAK_API_KEY=your-api-key # Ghasedak API key
```

Each driver package ships its own config file — `config/laravel-sms-gateway-ghasedak.php`
here — so no changes to `config/services.php` are needed.

Send through the default driver:

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver()->send([
    'message' => 'Hello',
    'receptor' => '09123456789',
]);
```

See the original provider documentation for available fields.

## Configuration

Set `SMS_GATEWAY_DRIVER` in your application's `.env` file to choose the default driver.

Provider environment keys are defined by each driver package — see that package's README (linked under [Driver Packages](#driver-packages)) for its variables.

The HTTP timeouts shared by every driver can be tuned in seconds:

```env
SMS_GATEWAY_TIMEOUT=10 # Request timeout
SMS_GATEWAY_CONNECT_TIMEOUT=5 # Connection timeout
```

Individual drivers may override these in `configureRequest()`.

### Switching Drivers

Use `driver()` to send through a specific driver without changing the default:

```php
SmsGateway::driver('ghasedak')->send($data);

SmsGateway::driver('kavenegar')->send($data);
```

### HTTP Client Access

Use `request()` to send directly through the configured Laravel HTTP client for a driver:

```php
$response = SmsGateway::driver('ghasedak')
    ->request()
    ->post('sms/send/simple', $data);
```

Use either `send()` or `request()` — after calling `request()->post(...)`, you do not need to call `send()`.

## Events

HTTP drivers dispatch `Misaf\LaravelSmsGateway\Events\SmsSent`.

```php
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class StoreSmsGatewayResult
{
    public function handle(SmsSent $event): void
    {
        $driver = $event->driverName;
        $url = $event->request->url();
        $status = $event->response->status();
        $body = $event->response->json();
    }
}
```

Event properties:

- `$driverName`: the resolved SMS gateway driver name.
- `$request`: the `Illuminate\Http\Client\Request` instance.
- `$response`: the `Illuminate\Http\Client\Response` instance.

## Custom Drivers

Custom drivers should extend `SmsGatewayDriver` (which implements the
`Misaf\LaravelSmsGateway\Contracts\SmsGateway` contract), implement `send()`, and use
`configureRequest()` for driver-specific HTTP client options.

The driver name is taken from the `extend()` registration key; it selects the `laravel-sms-gateway-{name}` config file and labels `SmsSent` events. Override `driverName()` to change both, or `configKey()` to point only the config lookup elsewhere.

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class CustomDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://api.example.com';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request->withToken($this->driverConfig('token'));
    }
}
```

Register it from a service provider:

```php
use App\SmsGateways\CustomDriver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        SmsGateway::extend('custom', function (Application $app): CustomDriver {
            return $app->make(CustomDriver::class);
        });
    }
}
```

## Repository Layout

This repository is a monorepo. The core package lives at the root and every
first-party driver lives in `src/Drivers/laravel-sms-gateway-<driver>`, split
out to its own read-only repository on release.

```
config/laravel-sms-gateway.php
src/
    Contracts/SmsGateway.php
    Drivers/
        NullSmsGatewayDriver.php
        laravel-sms-gateway-<driver>/     # a full composer package
    Events/SmsSent.php
    Facades/SmsGateway.php
    Providers/SmsGatewayServiceProvider.php
    SmsGatewayDriver.php
    SmsGatewayManager.php
tests/
```

## Testing

Run everything from the monorepo root — the test suite, static analysis, and
code style cover the core package and every driver package at once:

```bash
composer test
composer analyse
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

MIT
