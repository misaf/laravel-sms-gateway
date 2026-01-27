# Laravel SMS Gateway

A flexible and easy-to-use SMS gateway package for Laravel applications. This package provides a unified interface for sending SMS messages through multiple providers, making it simple to switch between different SMS services or use multiple providers simultaneously.

## Features

- 🚀 **Multiple Driver Support**: Built-in support for Ghasedak and Sunway SMS providers
- 🔌 **Extensible Architecture**: Easy to add custom SMS drivers
- 🎯 **Laravel Integration**: Seamless integration with Laravel's service container
- 📝 **Facade Support**: Clean and intuitive API using Laravel facades
- ⚙️ **Configuration Management**: Environment-based configuration
- 🔒 **Type Safe**: Built with strict types and modern PHP practices

## Requirements

- PHP >= 8.2
- Laravel >= 10.0

## Installation

You can install the package via Composer:

```bash
composer require misaf/laravel-sms-gateway
```

## Configuration

### Publish Configuration

Publish the configuration file to your `config` directory:

```bash
php artisan vendor:publish --tag=laravel-sms-gateway-config
```

Or manually publish the config file:

```bash
php artisan vendor:publish --provider="Misaf\LaravelSmsGateway\SmsGatewayServiceProvider"
```

### Environment Variables

Add the following environment variables to your `.env` file:

```env
# Default SMS Gateway Driver
SMS_GATEWAY_DRIVER=ghasedak

# Ghasedak Configuration
SMS_GATEWAY_GHASEDAK_APIKEY=your-api-key
SMS_GATEWAY_GHASEDAK_LINENUMBER=your-line-number

# Sunway Configuration
SMS_GATEWAY_SUNWAY_GATEWAY=https://sms.sunwaysms.com/smsws/HttpService.ashx
SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
SMS_GATEWAY_SUNWAY_SPECIALNUMBER=your-special-number
```

## Usage

### Using the Facade

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

// Send SMS using the default driver
SmsGateway::driver()->send()
    ->get('HttpService.ashx', [
        'method' => 'SendSMS',
        'mobile' => '09123456789',
        'message' => 'Hello, World!'
    ]);

// Use a specific driver
SmsGateway::driver('sunway')->send()
    ->get('HttpService.ashx', [
        'method' => 'SendSMS',
        'mobile' => '09123456789',
        'message' => 'Hello, World!'
    ]);
```

### Using Dependency Injection

```php
use Misaf\LaravelSmsGateway\SmsGatewayManager;

class SmsController extends Controller
{
    public function __construct(
        private SmsGatewayManager $smsGateway
    ) {}

    public function sendSms()
    {
        $this->smsGateway->driver()->send()
            ->get('HttpService.ashx', [
                'method' => 'SendSMS',
                'mobile' => '09123456789',
                'message' => 'Hello, World!'
            ]);
    }
}
```

### Using the Service Container

```php
$smsGateway = app('sms-gateway');

$smsGateway->driver('ghasedak')->send()
    ->get('HttpService.ashx', [
        'method' => 'SendSMS',
        'mobile' => '09123456789',
        'message' => 'Hello, World!'
    ]);
```

## Available Drivers

### Ghasedak

The Ghasedak driver is configured with:
- `apiKey`: Your Ghasedak API key
- `linenumber`: Your Ghasedak line number

### Sunway

The Sunway driver is configured with:
- `gateway`: The Sunway SMS gateway URL
- `username`: Your Sunway username
- `password`: Your Sunway password
- `special_number`: Your Sunway special number

## Creating Custom Drivers

To create a custom driver, implement the `SmsGatewayHandlerInterface`:

```php
<?php

namespace App\SmsGateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

class CustomDriver implements SmsGatewayHandlerInterface
{
    public function send(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . config('sms-gateway.drivers.custom.api_key'),
        ])->baseUrl('https://api.example.com')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
```

Then register your driver in the `SmsGatewayManager`:

```php
protected function createCustomDriver()
{
    return new CustomDriver();
}
```

## Testing

Run the test suite:

```bash
composer test
```

## Code Style

This package uses Laravel Pint for code style. Format your code:

```bash
composer pint
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

- **Issues**: [GitHub Issues](https://github.com/misaf/laravel-sms-gateway/issues)
- **Source**: [GitHub Repository](https://github.com/misaf/laravel-sms-gateway)

## Author

**Ehsan Mahmoodi**

- Email: misaf.1990@gmail.com

---

Made with ❤️ for the Laravel community
