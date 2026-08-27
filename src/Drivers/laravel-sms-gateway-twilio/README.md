# Laravel SMS Gateway Twilio Driver

Twilio SMS gateway driver for [`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway).

## Installation

```bash
composer require misaf/laravel-sms-gateway-twilio
```

Laravel package discovery registers the driver service provider automatically.

## Configuration

```env
SMS_GATEWAY_DRIVER=twilio
SMS_GATEWAY_TWILIO_ACCOUNT_SID=your-account-sid
SMS_GATEWAY_TWILIO_AUTH_TOKEN=your-auth-token
```

Publish the config file if you want to edit it directly:

```bash
php artisan vendor:publish --tag=sms-gateway-twilio-config
```

```php
<?php

declare(strict_types=1);

return [
    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID'),
    'auth_token'  => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN'),
    'base_url'    => env('SMS_GATEWAY_TWILIO_BASE_URL'),
];
```

By default, the account SID is included in the base URL path. If you override `base_url`, include the account-specific path segment expected by Twilio.

## Driver Behavior

| Option | Value |
| --- | --- |
| Driver name | `twilio` |
| Default base URL | `https://api.twilio.com/2010-04-01/Accounts/{account_sid}/` |
| `send()` endpoint | `POST Messages.json` |
| Authentication | HTTP Basic auth from `laravel-sms-gateway-twilio.account_sid` and `laravel-sms-gateway-twilio.auth_token` |
| Payload | Form data sent directly to Twilio |

## Usage

```php
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

$response = SmsGateway::driver('twilio')->send([
    'To'  => '+15005550006',
    'From' => '+15005550001',
    'Body' => 'Here is a test message.',
]);
```

The payload is passed directly to Twilio, so use the fields expected by the Twilio API.

Use `request()` when you need direct access to Laravel's HTTP client:

```php
$request = SmsGateway::driver('twilio')->request();
```

## Development

This package is developed in the
[`misaf/laravel-sms-gateway`](https://github.com/misaf/laravel-sms-gateway)
monorepo at `src/Drivers/laravel-sms-gateway-twilio` and split out here on release. Open issues and
pull requests against the monorepo; run `composer test` and `composer analyse`
from its root.

## License

MIT
