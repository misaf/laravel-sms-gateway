# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel. The core package provides the manager, facade, driver contract, shared HTTP driver base, configuration file, and `SmsSent` event. Provider packages register concrete drivers such as Ghasedak, Sunway, Kavenegar, and Sms.ir.

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13

## Installation

Install the core package:

```bash
composer require misaf/laravel-sms-gateway
```

Install the provider packages you need:

```bash
composer require misaf/laravel-sms-gateway-ghasedak
composer require misaf/laravel-sms-gateway-sunway
composer require misaf/laravel-sms-gateway-kavenegar
composer require misaf/laravel-sms-gateway-smsir
```

Laravel package discovery registers the package service providers automatically.

## Configuration

Publish the shared configuration file:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

Set the default driver and provider credentials in `.env`:

```env
SMS_GATEWAY_DRIVER=ghasedak

SMS_GATEWAY_TIMEOUT=10
SMS_GATEWAY_CONNECT_TIMEOUT=5

SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key
SMS_GATEWAY_GHASEDAK_LINENUMBER=3000xxxx
SMS_GATEWAY_GHASEDAK_TIMEOUT=10
SMS_GATEWAY_GHASEDAK_CONNECT_TIMEOUT=5

SMS_GATEWAY_SUNWAY_GATEWAY=https://sms.sunwaysms.com/smsws/HttpService.ashx
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
SMS_GATEWAY_SUNWAY_SPECIALNUMBER=3000xxxx
SMS_GATEWAY_SUNWAY_TIMEOUT=10
SMS_GATEWAY_SUNWAY_CONNECT_TIMEOUT=5

SMS_GATEWAY_KAVENEGAR_API_KEY=your-api-key
SMS_GATEWAY_KAVENEGAR_GATEWAY=https://api.kavenegar.com/v1/
SMS_GATEWAY_KAVENEGAR_TIMEOUT=10
SMS_GATEWAY_KAVENEGAR_CONNECT_TIMEOUT=5

SMS_GATEWAY_SMSIR_API_KEY=your-api-key
SMS_GATEWAY_SMSIR_API_KEY_HEADER=X-API-KEY
SMS_GATEWAY_SMSIR_GATEWAY=https://api.sms.ir/v1/
SMS_GATEWAY_SMSIR_TIMEOUT=10
SMS_GATEWAY_SMSIR_CONNECT_TIMEOUT=5
```

All drivers read from `config/sms_gateway.php` under the `sms_gateway.drivers` key. The configured driver name must match the name registered by the provider package.

`SMS_GATEWAY_TIMEOUT` and `SMS_GATEWAY_CONNECT_TIMEOUT` define shared HTTP client defaults. Each provider can override those values with its own timeout variables.

## Usage

The facade resolves the default driver from `sms_gateway.default`:

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

SmsGateway::driver()->send()->post('sms/send/simple', [
    'message'  => 'Hello',
    'receptor' => '09123456789',
]);
```

You may also choose a driver explicitly:

```php
SmsGateway::driver('sunway')->send()->get('', [
    'method'  => 'SendSMS',
    'mobile'  => '09123456789',
    'message' => 'Hello',
]);
```

The `send()` method returns Laravel's `Illuminate\Http\Client\PendingRequest`, so you can use the normal HTTP client methods for requests, retries, headers, query parameters, and response handling.

## Available Drivers

| Driver | Package |
| --- | --- |
| `ghasedak` | `misaf/laravel-sms-gateway-ghasedak` |
| `sunway` | `misaf/laravel-sms-gateway-sunway` |
| `kavenegar` | `misaf/laravel-sms-gateway-kavenegar` |
| `smsir` | `misaf/laravel-sms-gateway-smsir` |

## Events

Drivers that extend `Misaf\LaravelSmsGateway\HttpSmsGatewayDriver` dispatch `Misaf\LaravelSmsGateway\Events\SmsSent` after the HTTP client receives a response.

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

## Dependency Injection

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

final class SendWelcomeSms
{
    public function __construct(private SmsGatewayManager $gateway) {}

    public function handle(string $mobile): void
    {
        $this->gateway->driver('ghasedak')->send()->post('sms/send/simple', [
            'message'  => 'Welcome',
            'receptor' => $mobile,
        ]);
    }
}
```

## Custom Drivers

Application-specific drivers may implement `SmsGatewayHandlerInterface` directly:

```php
namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class CustomDriver implements SmsGatewayHandlerInterface
{
    public function send(): PendingRequest
    {
        return Http::withToken(Config::string('sms_gateway.drivers.custom.api_key', ''))
            ->baseUrl(Config::string('sms_gateway.drivers.custom.gateway', 'https://api.example.com'))
            ->timeout(Config::integer('sms_gateway.drivers.custom.timeout', 10))
            ->connectTimeout(Config::integer('sms_gateway.drivers.custom.connect_timeout', 5));
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

If your custom driver should use the shared timeout/base URL behavior and dispatch `SmsSent`, extend `HttpSmsGatewayDriver` instead of implementing the interface directly.

## Testing and Formatting

```bash
composer test
composer format
```

## License

MIT
