<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;
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

test('dispatches an event after an SMS gateway request receives a response', function (): void {
    Event::fake([
        SmsSent::class,
    ]);

    Http::fake([
        'https://events.example.com/messages' => Http::response(['message_id' => 'sms-123'], 202),
    ]);

    SmsGateway::extend('eventful', fn(): SmsGatewayHandlerInterface => new class extends HttpSmsGatewayDriver
    {
        protected function driverName(): string
        {
            return 'eventful';
        }

        protected function defaultGateway(): string
        {
            return 'https://events.example.com/';
        }
    });

    SmsGateway::driver('eventful')->send()
        ->post('messages', [
            'message' => 'Hello from event test',
            'to'      => '09123456789',
        ]);

    Event::assertDispatched(function (SmsSent $event): bool {
        return 'eventful' === $event->driverName
            && 'POST' === $event->request->method()
            && 'https://events.example.com/messages' === $event->request->url()
            && 'Hello from event test' === $event->request['message']
            && 202 === $event->response->status()
            && 'sms-123' === $event->response->json('message_id');
    });
});
