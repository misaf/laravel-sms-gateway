<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\ConfigurableDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\InvalidDriver;

test('can register a custom driver via extend', function (): void {
    Http::fake([
        'https://custom.example.com/health' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::extend('custom', function (Application $app): SmsGatewayHandlerInterface {
        return $app->make(ConfigurableDriver::class);
    });

    $result = SmsGateway::driver('custom')->send()
        ->get('health')
        ->json();

    expect($result)->toBe(['ok' => true]);
});

test('throws an exception when an extended driver is invalid', function (): void {
    SmsGateway::extend('invalid', function (Application $app): object {
        return $app->make(InvalidDriver::class);
    });

    expect(fn(): mixed => SmsGateway::driver('invalid'))
        ->toThrow(InvalidArgumentException::class, 'must implement');
})->skip();

test('falls back to ghasedak when default driver key is missing', function (): void {
    config()->set('sms_gateway', [
        'drivers' => config('sms_gateway.drivers'),
    ]);

    expect(app('sms-gateway')->getDefaultDriver())->toBe('ghasedak');
});
