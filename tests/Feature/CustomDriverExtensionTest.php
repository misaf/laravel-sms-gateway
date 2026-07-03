<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\ConfigurableDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\InvalidDriver;

test('can register a custom driver via extend', function (): void {
    Http::fake([
        'https://custom.example.com/health' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::extend('custom', function (Application $app): SmsGatewayHandlerInterface {
        return $app->make(ConfigurableDriver::class);
    });

    $result = SmsGateway::driver('custom')->request()
        ->get('health')
        ->json();

    expect($result)->toBe(['ok' => true]);
});

test('returns custom driver instances using laravel manager behavior', function (): void {
    SmsGateway::extend('invalid', function (Application $app): object {
        return $app->make(InvalidDriver::class);
    });

    expect(SmsGateway::driver('invalid'))->toBeInstanceOf(InvalidDriver::class);
});

test('uses the configured default driver', function (): void {
    config()->set('sms_gateway.default', 'sunway');

    expect(app('sms-gateway')->getDefaultDriver())->toBe('sunway');
});

test('returns null when no default driver is configured', function (): void {
    config()->set('sms_gateway.default', null);

    expect(SmsGateway::getDefaultDriver())->toBeNull();
});

test('defines shared HTTP client timeout defaults in config', function (): void {
    expect(config('sms_gateway.defaults'))
        ->toMatchArray([
            'timeout'         => 10,
            'connect_timeout' => 5,
        ]);
});

test('does not define per driver config defaults out of the box', function (): void {
    expect(config('sms_gateway.drivers'))->toBeNull();
});

test('dispatches an event after an SMS gateway request receives a response', function (): void {
    Event::fake([
        SmsSent::class,
    ]);

    Http::fake([
        'https://events.example.com/messages' => Http::response(['message_id' => 'sms-123'], 202),
    ]);

    SmsGateway::extend('eventful', fn(): SmsGatewayHandlerInterface => new class () extends SmsGatewayDriver {
        /**
         * @param array<string, mixed> $data
         */
        public function send(array $data): Response
        {
            return $this->request()->post('messages', $data);
        }

        protected function driverName(): string
        {
            return 'eventful';
        }

        protected function defaultBaseUrl(): string
        {
            return 'https://events.example.com/';
        }
    });

    SmsGateway::driver('eventful')->send([
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

test('prefers the base URL configured in services over the driver default', function (): void {
    config()->set('sms_gateway.default', 'overrideable');
    config()->set('services.overrideable.api_key', 'test-api-key');
    config()->set('services.overrideable.base_url', 'https://services-override.example.test/v1/');

    Http::fake([
        'https://services-override.example.test/v1/sms/send.json' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::extend('overrideable', fn(): SmsGatewayHandlerInterface => new class () extends SmsGatewayDriver {
        /**
         * @param array<string, mixed> $data
         */
        public function send(array $data): Response
        {
            return $this->request()->post('sms/send.json', $data);
        }

        protected function driverName(): string
        {
            return 'overrideable';
        }

        protected function defaultBaseUrl(): string
        {
            return 'https://driver-default.example.test/v1/';
        }

        protected function apiKeyHeader(): string
        {
            return 'apikey';
        }
    });

    SmsGateway::driver()->send([
        'receptor' => '09123456789',
        'message'  => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/v1/sms/send.json' === $request->url()
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('can use a different HTTP method for send', function (): void {
    Http::fake([
        'https://send.example.test/v1/messages?message=Hello%20from%20send%20test&to=09123456789' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::extend('overridden-sendable', fn(): SmsGatewayHandlerInterface => new class () extends SmsGatewayDriver {
        /**
         * @param array<string, mixed> $data
         */
        public function send(array $data): Response
        {
            return $this->request()->get('messages', $data);
        }

        protected function driverName(): string
        {
            return 'overridden-sendable';
        }

        protected function defaultBaseUrl(): string
        {
            return 'https://send.example.test/v1/';
        }
    });

    SmsGateway::driver('overridden-sendable')->send([
        'message' => 'Hello from send test',
        'to'      => '09123456789',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'GET' === $request->method()
            && 'https://send.example.test/v1/messages?message=Hello%20from%20send%20test&to=09123456789' === $request->url();
    });
});
