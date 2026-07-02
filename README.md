# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel 13.

The package provides:

- A `SmsGateway` facade and injectable `SmsGatewayManager`.
- Built-in HTTP drivers.
- A common `PendingRequest` API through Laravel's HTTP client.
- A `SmsSent` event for HTTP-based drivers.
- A small driver contract for custom gateways.

## Requirements

- PHP 8.3+
- Laravel 13+

## Installation

```bash
composer require misaf/laravel-sms-gateway
```

Laravel package discovery registers `Misaf\LaravelSmsGateway\SmsGatewayServiceProvider` automatically.

Publish the package configuration when you need to override the defaults:

```bash
php artisan vendor:publish --tag=sms-gateway-config
```

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
    'line_number' => env('SMS_GATEWAY_GHASEDAK_LINENUMBER'),
],
```

Send a request through the default driver:

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

$response = SmsGateway::driver()->send()->post('sms/send/simple', [
    'message'  => 'Hello',
    'receptor' => '09123456789',
]);
```

Select a driver explicitly when needed:

```php
SmsGateway::driver('sunway')->send()->get('', [
    'method'  => 'SendSMS',
    'mobile'  => '09123456789',
    'message' => 'Hello',
]);
```

`send()` returns an `Illuminate\Http\Client\PendingRequest`, so you can continue with Laravel HTTP client methods such as `get`, `post`, `retry`, `timeout`, and `withHeaders`.

## Configuration

The package reads `sms_gateway.default` to choose the default driver. The published config file uses `SMS_GATEWAY_DRIVER` and defaults to `ghasedak`.

HTTP drivers use these shared timeout values:

```env
SMS_GATEWAY_TIMEOUT=10
SMS_GATEWAY_CONNECT_TIMEOUT=5
```

Per-driver values resolve in this order:

1. `services.{driver}.{key}` from `config/services.php`.
2. `sms_gateway.drivers.{driver}.{camelCaseKey}` from `config/sms_gateway.php`.
3. The driver's built-in default, when one exists.

Set `services.{driver}.gateway` to override a provider's default gateway URL.

## Service Credentials

Add only the providers you use to `config/services.php`.

| Driver | Required service keys | Environment variables |
| --- | --- | --- |
| `ghasedak` | `api_key`, optional `line_number` | `SMS_GATEWAY_GHASEDAK_APIKEY`, `SMS_GATEWAY_GHASEDAK_LINENUMBER` |
| `sunway` | `username`, `password`, optional `special_number` | `SMS_GATEWAY_SUNWAY_USERNAME`, `SMS_GATEWAY_SUNWAY_PASSWORD`, `SMS_GATEWAY_SUNWAY_SPECIALNUMBER` |
| `kavenegar` | `api_key` | `SMS_GATEWAY_KAVENEGAR_API_KEY` |
| `smsir` | `api_key`, optional `api_key_header` | `SMS_GATEWAY_SMSIR_API_KEY`, `SMS_GATEWAY_SMSIR_API_KEY_HEADER` |
| `twilio` | `account_sid`, `auth_token` | `SMS_GATEWAY_TWILIO_ACCOUNT_SID`, `SMS_GATEWAY_TWILIO_AUTH_TOKEN` |
| `vonage` | `api_key`, `api_secret` | `SMS_GATEWAY_VONAGE_API_KEY`, `SMS_GATEWAY_VONAGE_API_SECRET` |
| `plivo` | `auth_id`, `auth_token` | `SMS_GATEWAY_PLIVO_AUTH_ID`, `SMS_GATEWAY_PLIVO_AUTH_TOKEN` |
| `messagebird` | `access_key` | `SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY` |
| `textlocal` | `api_key` | `SMS_GATEWAY_TEXTLOCAL_API_KEY` |
| `melipayamak` | `username`, `password` | `SMS_GATEWAY_MELIPAYAMAK_USERNAME`, `SMS_GATEWAY_MELIPAYAMAK_PASSWORD` |
| `ippanel` | `username`, `password` | `SMS_GATEWAY_IPPANEL_USERNAME`, `SMS_GATEWAY_IPPANEL_PASSWORD` |
| `magfa` | `username`, `password` | `SMS_GATEWAY_MAGFA_USERNAME`, `SMS_GATEWAY_MAGFA_PASSWORD` |

Example `config/services.php` entries:

```php
'twilio' => [
    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID'),
    'auth_token' => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN'),
],

'smsir' => [
    'api_key' => env('SMS_GATEWAY_SMSIR_API_KEY'),
    'api_key_header' => env('SMS_GATEWAY_SMSIR_API_KEY_HEADER', 'X-API-KEY'),
],
```

## Built-In Drivers

| Driver | Default gateway | Request behavior |
| --- | --- | --- |
| `ghasedak` | `https://api.ghasedak.me/v2/` | Sends `api_key` in the `apikey` header. |
| `sunway` | `https://sms.sunwaysms.com/smsws/HttpService.ashx` | Adds `UserName` and `Password` query parameters. |
| `kavenegar` | `https://api.kavenegar.com/v1/` | Sends `api_key` in the `apikey` header. |
| `smsir` | `https://api.sms.ir/v1/` | Sends `api_key` in `X-API-KEY` by default and accepts JSON. |
| `twilio` | `https://api.twilio.com/2010-04-01/Accounts/{account_sid}/` | Uses HTTP basic auth and form requests. |
| `vonage` | `https://rest.nexmo.com/` | Adds `api_key` and `api_secret` query parameters and accepts JSON. |
| `plivo` | `https://api.plivo.com/v1/Account/{auth_id}/` | Uses HTTP basic auth and JSON requests. |
| `messagebird` | `https://rest.messagebird.com/` | Sends `Authorization: AccessKey {access_key}` and JSON requests. |
| `textlocal` | `https://api.txtlocal.com/` | Adds `apikey` as a query parameter and sends form requests. |
| `melipayamak` | `https://rest.payamak-panel.com/api/` | Adds `username` and `password` query parameters and sends form requests. |
| `ippanel` | `https://ippanel.com/services.jspd` | Adds `uname` and `pass` query parameters and sends form requests. |
| `magfa` | `https://sms.magfa.com/api/http/sms/v2/` | Uses HTTP basic auth and JSON requests. |

Twilio scopes its base URL to the configured account, so request paths are relative to that account:

```php
SmsGateway::driver('twilio')->send()->post('Messages.json', [
    'To'   => '+15005550006',
    'From' => '+15005550001',
    'Body' => 'Hello',
]);
```

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
        $this->gateway->driver('ghasedak')->send()->post('sms/send/simple', [
            'message'  => 'Welcome',
            'receptor' => $mobile,
        ]);
    }
}
```

## Events

Drivers extending `Misaf\LaravelSmsGateway\HttpSmsGatewayDriver` dispatch `Misaf\LaravelSmsGateway\Events\SmsSent` after the HTTP client receives a response.

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

Custom drivers must return a `PendingRequest` from `send()`.

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

Extend `HttpSmsGatewayDriver` when the custom driver should reuse gateway resolution, shared timeouts, API-key headers, and `SmsSent` dispatching:

```php
namespace App\SmsGateways;

use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class CustomHttpDriver extends HttpSmsGatewayDriver
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
}
```

For drivers extending `HttpSmsGatewayDriver`, values such as `services.custom.api_key`, `services.custom.gateway`, and `sms_gateway.drivers.custom.apiKey` are resolved automatically by the base class.

## Testing and Formatting

Run these commands from the package root:

```bash
composer test
composer analyse
```

Code style is checked and fixed by the `Fix Code Style` GitHub Actions workflow using Laravel Pint on PHP 8.3.

## License

MIT
