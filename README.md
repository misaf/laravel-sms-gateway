# Laravel SMS Gateway

A driver-based SMS gateway manager for Laravel. It provides a facade, injectable manager, driver contract, reusable HTTP driver base, `SmsSent` event, publishable configuration, and built-in drivers for Ghasedak, Sunway, Kavenegar, Sms.ir, Twilio, Vonage, Plivo, MessageBird, Textlocal, Melipayamak, IPPanel, and Magfa.

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13

## Installation

Install the package with Composer:

```bash
composer require misaf/laravel-sms-gateway
```

Laravel package discovery registers `Misaf\LaravelSmsGateway\SmsGatewayServiceProvider` automatically.

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

SMS_GATEWAY_SUNWAY_USERNAME=your-username
SMS_GATEWAY_SUNWAY_PASSWORD=your-password
SMS_GATEWAY_SUNWAY_SPECIALNUMBER=3000xxxx

SMS_GATEWAY_KAVENEGAR_API_KEY=your-api-key

SMS_GATEWAY_SMSIR_API_KEY=your-api-key
SMS_GATEWAY_SMSIR_API_KEY_HEADER=X-API-KEY

SMS_GATEWAY_TWILIO_ACCOUNT_SID=ACxxxxxxxx
SMS_GATEWAY_TWILIO_AUTH_TOKEN=your-auth-token

SMS_GATEWAY_VONAGE_API_KEY=your-api-key
SMS_GATEWAY_VONAGE_API_SECRET=your-api-secret

SMS_GATEWAY_PLIVO_AUTH_ID=MAxxxxxxxx
SMS_GATEWAY_PLIVO_AUTH_TOKEN=your-auth-token

SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY=your-access-key

SMS_GATEWAY_TEXTLOCAL_API_KEY=your-api-key

SMS_GATEWAY_MELIPAYAMAK_USERNAME=your-username
SMS_GATEWAY_MELIPAYAMAK_PASSWORD=your-password

SMS_GATEWAY_IPPANEL_USERNAME=your-username
SMS_GATEWAY_IPPANEL_PASSWORD=your-password

SMS_GATEWAY_MAGFA_USERNAME=your-username
SMS_GATEWAY_MAGFA_PASSWORD=your-password
```

Add the credentials you need to `config/services.php`, following Laravel's convention for third-party service credentials:

```php
'ghasedak' => [
    'api_key' => env('SMS_GATEWAY_GHASEDAK_APIKEY'),
    'line_number' => env('SMS_GATEWAY_GHASEDAK_LINENUMBER'),
],

'sunway' => [
    'username' => env('SMS_GATEWAY_SUNWAY_USERNAME'),
    'password' => env('SMS_GATEWAY_SUNWAY_PASSWORD'),
    'special_number' => env('SMS_GATEWAY_SUNWAY_SPECIALNUMBER'),
],

'kavenegar' => [
    'api_key' => env('SMS_GATEWAY_KAVENEGAR_API_KEY'),
],

'smsir' => [
    'api_key' => env('SMS_GATEWAY_SMSIR_API_KEY'),
    'api_key_header' => env('SMS_GATEWAY_SMSIR_API_KEY_HEADER', 'X-API-KEY'),
],

'twilio' => [
    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID'),
    'auth_token' => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN'),
],

'vonage' => [
    'api_key' => env('SMS_GATEWAY_VONAGE_API_KEY'),
    'api_secret' => env('SMS_GATEWAY_VONAGE_API_SECRET'),
],

'plivo' => [
    'auth_id' => env('SMS_GATEWAY_PLIVO_AUTH_ID'),
    'auth_token' => env('SMS_GATEWAY_PLIVO_AUTH_TOKEN'),
],

'messagebird' => [
    'access_key' => env('SMS_GATEWAY_MESSAGEBIRD_ACCESS_KEY'),
],

'textlocal' => [
    'api_key' => env('SMS_GATEWAY_TEXTLOCAL_API_KEY'),
],

'melipayamak' => [
    'username' => env('SMS_GATEWAY_MELIPAYAMAK_USERNAME'),
    'password' => env('SMS_GATEWAY_MELIPAYAMAK_PASSWORD'),
],

'ippanel' => [
    'username' => env('SMS_GATEWAY_IPPANEL_USERNAME'),
    'password' => env('SMS_GATEWAY_IPPANEL_PASSWORD'),
],

'magfa' => [
    'username' => env('SMS_GATEWAY_MAGFA_USERNAME'),
    'password' => env('SMS_GATEWAY_MAGFA_PASSWORD'),
],
```

Each driver setting resolves in this order:

1. `services.{driver}.{key}` — your configured value (takes precedence).
2. `sms_gateway.drivers.{driver}.{camelCaseKey}` — an optional shared default in `config/sms_gateway.php`.
3. The driver's built-in default (e.g. its default gateway URL).

Each built-in driver targets its provider's gateway out of the box. To point one at a different endpoint, set `services.{driver}.gateway` to the URL. The configured driver name must match a registered driver.

`SMS_GATEWAY_TIMEOUT` and `SMS_GATEWAY_CONNECT_TIMEOUT` define shared HTTP client defaults for all built-in HTTP drivers.

## Usage

The facade resolves the default driver from `sms_gateway.default`:

```php
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

$response = SmsGateway::driver()->send()->post('sms/send/simple', [
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

| Driver | Default gateway | Notes |
| --- | --- | --- |
| `ghasedak` | `https://api.ghasedak.me/v2/` | Sends the configured API key in the `apikey` header. |
| `sunway` | `https://sms.sunwaysms.com/smsws/HttpService.ashx` | Adds `UserName` and `Password` query parameters from `services.sunway`. |
| `kavenegar` | `https://api.kavenegar.com/v1/` | Sends the configured API key in the `apikey` header. |
| `smsir` | `https://api.sms.ir/v1/` | Sends the configured API key in `X-API-KEY` by default and accepts JSON. |
| `twilio` | `https://api.twilio.com/2010-04-01/Accounts/{account_sid}/` | Uses HTTP basic auth and form-encoded requests. |
| `vonage` | `https://rest.nexmo.com/` | Adds `api_key` and `api_secret` query parameters from `services.vonage`. |
| `plivo` | `https://api.plivo.com/v1/Account/{auth_id}/` | Uses HTTP basic auth and JSON requests. |
| `messagebird` | `https://rest.messagebird.com/` | Sends `Authorization: AccessKey {access_key}` and JSON requests. |
| `textlocal` | `https://api.txtlocal.com/` | Adds the configured API key as an `apikey` query parameter and sends form-encoded requests. |
| `melipayamak` | `https://rest.payamak-panel.com/api/` | Adds `username` and `password` query parameters from `services.melipayamak` and sends form-encoded requests. |
| `ippanel` | `https://ippanel.com/services.jspd` | Adds `uname` and `pass` query parameters from `services.ippanel` and sends form-encoded requests. |
| `magfa` | `https://sms.magfa.com/api/http/sms/v2/` | Uses HTTP basic auth and JSON requests. |

The `twilio` driver authenticates with HTTP basic auth (`services.twilio.account_sid` and `services.twilio.auth_token`), sends form-encoded bodies, and scopes its base URL to the configured account, so requests target paths like `Messages.json` directly:

```php
SmsGateway::driver('twilio')->send()->post('Messages.json', [
    'To'   => '+15005550006',
    'From' => '+15005550001',
    'Body' => 'Hello',
]);
```

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

If your custom driver should use the shared timeout/base URL behavior and dispatch `SmsSent`, extend `HttpSmsGatewayDriver` instead of implementing the interface directly.

## Testing and Formatting

```bash
composer test
composer analyse
composer format
```

## License

MIT
