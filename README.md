# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel 13.

The core package provides the Laravel-facing SMS gateway manager, facade, driver contract, reusable HTTP driver base class, and send event. Provider-specific integrations are installed as separate Composer packages, so applications only pull in the gateways they actually use.

## Features

- `SmsGateway` facade and injectable `SmsGatewayManager`.
- Driver registration through Laravel's manager `extend()` API.
- Shared `PendingRequest` API through Laravel's HTTP client.
- Reusable `SmsGatewayDriver` base class for HTTP-based gateways.
- `SmsSent` event for drivers that extend the base HTTP driver.
- Small `SmsGatewayHandlerInterface` contract for custom drivers.

## Requirements

- PHP 8.3+
- Laravel 13+

## Installation

Install the core package:

```bash
composer require misaf/laravel-sms-gateway
```

Install one or more driver packages:

```bash
composer require misaf/laravel-sms-gateway-ghasedak
composer require misaf/laravel-sms-gateway-kavenegar
```

Laravel package discovery registers the core service provider and installed driver service providers automatically.

Publish the core configuration when you need to override defaults:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

## Driver Packages

Available first-party driver packages:

| Driver | Package |
| --- | --- |
| `ghasedak` | `misaf/laravel-sms-gateway-ghasedak` |
| `sunway` | `misaf/laravel-sms-gateway-sunway` |
| `kavenegar` | `misaf/laravel-sms-gateway-kavenegar` |
| `smsir` | `misaf/laravel-sms-gateway-smsir` |
| `twilio` | `misaf/laravel-sms-gateway-twilio` |
| `vonage` | `misaf/laravel-sms-gateway-vonage` |
| `plivo` | `misaf/laravel-sms-gateway-plivo` |
| `messagebird` | `misaf/laravel-sms-gateway-messagebird` |
| `textlocal` | `misaf/laravel-sms-gateway-textlocal` |
| `melipayamak` | `misaf/laravel-sms-gateway-melipayamak` |
| `ippanel` | `misaf/laravel-sms-gateway-ippanel` |
| `magfa` | `misaf/laravel-sms-gateway-magfa` |

## Quick Start

Set the default driver in `.env`:

```env
SMS_GATEWAY_DRIVER=ghasedak
SMS_GATEWAY_TIMEOUT=10
SMS_GATEWAY_CONNECT_TIMEOUT=5
SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key
```

Add the matching service credentials in `config/services.php`:

```php
'ghasedak' => [
    'api_key' => env('SMS_GATEWAY_GHASEDAK_APIKEY'),
],
```

Send through the default driver:

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

$response = SmsGateway::driver()->send([
    'message'  => 'Hello',
    'receptor' => '09123456789',
]);
```

Select a driver explicitly when needed:

```php
$response = SmsGateway::driver('kavenegar')->send([
    'receptor' => '09123456789',
    'message'  => 'Hello',
]);
```

Use the underlying Laravel HTTP client request when you need lower-level control:

```php
$response = SmsGateway::driver('kavenegar')
    ->request()
    ->retry(3, 200)
    ->post('sms/send.json', [
        'receptor' => '09123456789',
        'message'  => 'Hello',
    ]);
```

`request()` returns an `Illuminate\Http\Client\PendingRequest`, so you can continue with Laravel HTTP client methods such as `get`, `post`, `retry`, `timeout`, `withHeaders`, and `withOptions`.

## Configuration

The package reads `sms_gateway.default` to choose the default driver. The published config file uses `SMS_GATEWAY_DRIVER` and defaults to an empty string until you install and configure a driver.

HTTP drivers use these shared timeout values:

```env
SMS_GATEWAY_TIMEOUT=10
SMS_GATEWAY_CONNECT_TIMEOUT=5
```

Drivers extending `SmsGatewayDriver` resolve per-driver values in this order:

1. `services.{driver}.{key}` from `config/services.php`.
2. `sms_gateway.drivers.{driver}.{camelCaseKey}` from `config/sms_gateway.php`.
3. The driver's built-in default, when one exists.

For example:

```php
// config/services.php
'ghasedak' => [
    'api_key' => env('SMS_GATEWAY_GHASEDAK_APIKEY'),
    'gateway' => env('SMS_GATEWAY_GHASEDAK_GATEWAY'),
    'endpoints' => [
        'default' => env('SMS_GATEWAY_GHASEDAK_ENDPOINT', 'sms/send/simple'),
    ],
],
```

Set `services.{driver}.gateway` to override a provider's default gateway URL. Set `services.{driver}.endpoints.{name}` to override a built-in endpoint path.

Legacy package-level driver configuration can be kept in `config/sms_gateway.php`:

```php
'drivers' => [
    'ghasedak' => [
        'apiKey' => env('SMS_GATEWAY_GHASEDAK_APIKEY'),
        'gateway' => env('SMS_GATEWAY_GHASEDAK_GATEWAY'),
        'endpoints' => [
            'default' => 'sms/send/simple',
        ],
    ],
],
```

Service configuration still takes precedence over values in `sms_gateway.drivers`.

## Dependency Injection

The manager is bound in the service container as both `sms-gateway` and `Misaf\LaravelSmsGateway\SmsGatewayManager`.

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

final class SendWelcomeSms
{
    public function __construct(private SmsGatewayManager $gateway)
    {
    }

    public function handle(string $mobile): void
    {
        $this->gateway->driver()->send([
            'message'  => 'Welcome',
            'receptor' => $mobile,
        ]);
    }
}
```

## Events

Drivers extending `Misaf\LaravelSmsGateway\SmsGatewayDriver` dispatch `Misaf\LaravelSmsGateway\Events\SmsSent` after the HTTP client receives a response.

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

The event exposes:

- `$driverName`: the resolved SMS gateway driver name.
- `$request`: the `Illuminate\Http\Client\Request` instance.
- `$response`: the `Illuminate\Http\Client\Response` instance.

## Custom Drivers

Custom drivers must implement `send()` and `request()`.

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class CustomDriver implements SmsGatewayHandlerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data, ?string $endpoint = null): Response
    {
        return $this->request()->post($endpoint ?? 'messages', $data);
    }

    public function request(): PendingRequest
    {
        return Http::withToken(Config::string('services.custom.token'))
            ->baseUrl(Config::string('services.custom.gateway', 'https://api.example.com'))
            ->timeout(Config::integer('sms_gateway.defaults.timeout'))
            ->connectTimeout(Config::integer('sms_gateway.defaults.connect_timeout'));
    }
}
```

Register the driver from a service provider:

```php
use App\SmsGateways\CustomDriver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        SmsGateway::extend('custom', function (Application $app): SmsGatewayHandlerInterface {
            return $app->make(CustomDriver::class);
        });
    }
}
```

Extend `SmsGatewayDriver` when the custom driver should reuse gateway resolution, shared timeouts, API-key headers, endpoint resolution, and `SmsSent` dispatching:

```php
namespace App\SmsGateways;

use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class CustomSmsGatewayDriver extends SmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'custom';
    }

    protected function defaultGateway(): string
    {
        return 'https://api.example.com/';
    }

    protected function apiKeyHeader(): string
    {
        return 'X-API-Key';
    }

    /**
     * @return array<string, string>
     */
    protected function defaultEndpoints(): array
    {
        return [
            'default' => 'messages',
            'lookup' => 'messages/lookup',
        ];
    }
}
```

For drivers extending `SmsGatewayDriver`, values such as `services.custom.api_key`, `services.custom.gateway`, `services.custom.endpoints.default`, and `sms_gateway.drivers.custom.apiKey` are resolved automatically by the base class. If `apiKeyHeader()` returns a header name, the base class sends that header only when the resolved `api_key` value is not empty.

You can send through a named endpoint by passing the resolved endpoint to `send()`:

```php
/** @var \App\SmsGateways\CustomSmsGatewayDriver $driver */
$driver = SmsGateway::driver('custom');

$response = $driver->send(
    ['mobile' => '09123456789', 'code' => '123456'],
    $driver->endpoint('lookup'),
);
```

## Creating Driver Packages

A driver package should require this core package, provide a concrete driver class, and register itself with the manager from its package service provider:

```php
use Illuminate\Contracts\Foundation\Application;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Vendor\SmsGatewayExample\Drivers\ExampleDriver;

$this->app->afterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager, Application $app): void {
    $manager->extend('example', fn (): ExampleDriver => $app->make(ExampleDriver::class));
});
```

Add the driver package service provider to that package's `composer.json`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Vendor\\SmsGatewayExample\\SmsGatewayExampleServiceProvider"
            ]
        }
    }
}
```

## Testing and Analysis

Run these commands from the package root:

```bash
composer test
composer analyse
```

## License

MIT
