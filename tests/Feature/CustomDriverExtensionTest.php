<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Request;
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

    expect(fn (): mixed => SmsGateway::driver('invalid'))
        ->toThrow(InvalidArgumentException::class, 'must implement');
});

test('falls back to ghasedak when default driver key is missing', function (): void {
    config()->set('sms_gateway', [
        'defaults' => config('sms_gateway.defaults'),
        'drivers' => config('sms_gateway.drivers'),
    ]);

    expect(app('sms-gateway')->getDefaultDriver())->toBe('ghasedak');
});

test('defines shared HTTP client timeout defaults in config', function (): void {
    expect(config('sms_gateway.defaults'))
        ->toMatchArray([
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
});

test('does not define per driver HTTP client timeout values', function (): void {
    expect(config('sms_gateway.drivers'))
        ->each
        ->not->toHaveKeys([
            'timeout',
            'connect_timeout',
        ]);
});

test('falls back to legacy driver config when service credentials are empty', function (): void {
    config()->set('services.legacy.api_key', '');
    config()->set('sms_gateway.drivers.legacy.apiKey', 'legacy-api-key');

    Http::fake([
        'https://legacy.example.com/messages' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::extend('legacy', fn (): SmsGatewayHandlerInterface => new class extends HttpSmsGatewayDriver
    {
        protected function driverName(): string
        {
            return 'legacy';
        }

        protected function defaultGateway(): string
        {
            return 'https://legacy.example.com/';
        }

        /**
         * @return array<string, string>
         */
        protected function headers(): array
        {
            return [
                'apikey' => $this->serviceConfigString('api_key', 'apiKey'),
            ];
        }
    });

    SmsGateway::driver('legacy')->send()->get('messages');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://legacy.example.com/messages'
            && $request->hasHeader('apikey', 'legacy-api-key');
    });
});

test('dispatches an event after an SMS gateway request receives a response', function (): void {
    Event::fake([
        SmsSent::class,
    ]);

    Http::fake([
        'https://events.example.com/messages' => Http::response(['message_id' => 'sms-123'], 202),
    ]);

    SmsGateway::extend('eventful', fn (): SmsGatewayHandlerInterface => new class extends HttpSmsGatewayDriver
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
            'to' => '09123456789',
        ]);

    Event::assertDispatched(function (SmsSent $event): bool {
        return $event->driverName === 'eventful'
            && $event->request->method() === 'POST'
            && $event->request->url() === 'https://events.example.com/messages'
            && $event->request['message'] === 'Hello from event test'
            && $event->response->status() === 202
            && $event->response->json('message_id') === 'sms-123';
    });
});
