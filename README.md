# Laravel SMS Gateway

A driver-based SMS gateway package for Laravel built on `Illuminate\Support\Manager`.

## What This Package Provides

The core package provides:

- `SmsGatewayManager` for driver resolution
- `SmsGatewayHandlerInterface` as the driver contract
- `SmsGateway` facade for simple access
- shared configuration (`config/sms_gateway.php`)

The core package does not ship built-in providers directly. Drivers are registered by plugin packages through `SmsGateway::extend()` / manager extension.

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

Install the core package:

```bash
composer require misaf/laravel-sms-gateway
```

Install any driver plugins you need:

```bash
composer require \
  misaf/laravel-sms-gateway-ghasedak \
  misaf/laravel-sms-gateway-sunway \
  misaf/laravel-sms-gateway-kavenegar \
  misaf/laravel-sms-gateway-smsir
```

## Configuration

Publish configuration:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

All drivers use the same config file: `config/sms_gateway.php`.

### Example `.env`

```env
SMS_GATEWAY_DRIVER=ghasedak

SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key
SMS_GATEWAY_GHASEDAK_LINENUMBER=3000xxxx

SMS_GATEWAY_SUNWAY_GATEWAY=https://sms.sunwaysms.com/smsws/HttpService.ashx
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
SMS_GATEWAY_SUNWAY_SPECIALNUMBER=3000xxxx

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

## Usage

### Facade

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

SmsGateway::driver()->send()->post('sms/send/simple', [
    'message' => 'Hello',
    'receptor' => '09123456789',
]);

SmsGateway::driver('sunway')->send()->get('', [
    'method' => 'SendSMS',
    'mobile' => '09123456789',
    'message' => 'Hello',
]);
```

### Dependency Injection

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

final class SmsController
{
    public function __construct(private SmsGatewayManager $gateway)
    {
    }

    public function send(): void
    {
        $this->gateway->driver('ghasedak')->send()->post('sms/send/simple', [
            'message' => 'Hello',
            'receptor' => '09123456789',
        ]);
    }
}
```

### Service Container

```php
$gateway = app('sms-gateway');

$gateway->driver('kavenegar')->send();
```

## Driver Resolution (Manager Behavior)

- `driver()` resolves the default driver from `sms_gateway.default`.
- `driver('name')` resolves the given driver name.
- Driver names come from the key used when registering `extend('name', ...)`.
- If a driver cannot be resolved, Laravel Manager throws a driver resolution exception.
- If a resolved driver does not implement `SmsGatewayHandlerInterface`, the manager throws `InvalidArgumentException`.

## Built-In Plugin Drivers

- `ghasedak` via `misaf/laravel-sms-gateway-ghasedak`
- `sunway` via `misaf/laravel-sms-gateway-sunway`
- `kavenegar` via `misaf/laravel-sms-gateway-kavenegar`
- `smsir` via `misaf/laravel-sms-gateway-smsir`

## App-Level Custom Driver

Use this when custom logic belongs to your application, not a reusable package.

### 1. Create a driver class

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

### 2. Register the driver

```php
namespace App\Providers;

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

### 3. Add config

```php
// config/sms_gateway.php
'drivers' => [
    // ...
    'custom' => [
        'api_key' => env('SMS_GATEWAY_CUSTOM_API_KEY'),
        'gateway' => env('SMS_GATEWAY_CUSTOM_GATEWAY', 'https://api.example.com'),
        'timeout' => (int) env('SMS_GATEWAY_CUSTOM_TIMEOUT', 10),
        'connect_timeout' => (int) env('SMS_GATEWAY_CUSTOM_CONNECT_TIMEOUT', 5),
    ],
],
```

Then use:

```php
SmsGateway::driver('custom')->send();
```

## Creating a Separate Driver Package

Use this when the driver should be reusable across projects.

1. Create a new package (for example `misaf/laravel-sms-gateway-yourdriver`).
2. Require `misaf/laravel-sms-gateway` and `spatie/laravel-package-tools`.
3. Implement a driver class that implements `SmsGatewayHandlerInterface`.
4. In your package service provider, register with manager extension.

Example registration pattern:

```php
use Illuminate\Contracts\Foundation\Application;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;
use Misaf\LaravelSmsGateway\SmsGatewayManager;

$this->app->afterResolving('sms-gateway', function (SmsGatewayManager $manager): void {
    $manager->extend('yourdriver', function (Application $app): SmsGatewayHandlerInterface {
        return $app->make(YourDriver::class);
    });
});
```

No separate config file is required for each driver package. Use the main `sms_gateway.drivers` config and choose a unique key that matches your `extend('key', ...)` name.

## Testing and Formatting

```bash
composer test
composer format
```

## License

MIT
