# Laravel SMS Gateway

A simple driver-based SMS gateway manager for Laravel.

## Features

- Send SMS through a facade or manager.
- Separate packages for each provider.
- Laravel HTTP client access.
- Custom driver registration.

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
```

Publish the config file:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

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

Set the default driver in `.env`:

```env
SMS_GATEWAY_DRIVER=ghasedak # Default driver
SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key # Ghasedak API key
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

See the original provider documentation for available fields.

## Configuration

Use `.env` for environment values:

```env
SMS_GATEWAY_DRIVER=ghasedak # Default driver
SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key # Ghasedak API key
```

Provider keys like `SMS_GATEWAY_GHASEDAK_APIKEY` are defined by each driver package — see that package's README (linked under [Driver Packages](#driver-packages)) for its environment variables.

## Dependency Injection

Inject `Misaf\LaravelSmsGateway\SmsGatewayManager`:

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

Register it from a service provider:

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

## Creating Driver Packages

A driver package registers its driver from its service provider:

```php
use Illuminate\Contracts\Foundation\Application;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Vendor\SmsGatewayExample\Drivers\ExampleDriver;

$this->app->afterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager, Application $app): void {
    $manager->extend('example', fn (): ExampleDriver => $app->make(ExampleDriver::class));
});
```

Add the service provider to the driver package `composer.json`:

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

## Quality Checks

Run before committing:

```bash
vendor/bin/pint
composer test
composer analyse
```

## License

MIT
