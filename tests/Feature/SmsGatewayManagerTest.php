<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Drivers\NullSmsGatewayDriver;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\CustomSmsGatewayDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\NonGatewayDriver;

describe('driver resolution', function (): void {
    test('reads the default driver name from config', function (): void {
        config()->set('laravel-sms-gateway.default', 'sunway');

        expect(SmsGateway::getDefaultDriver())->toBe('sunway');
    });

    test('falls back to the null driver when none is configured', function (): void {
        config()->set('laravel-sms-gateway.default', null);

        expect(SmsGateway::getDefaultDriver())->toBe('null');
        expect(SmsGateway::driver())->toBeInstanceOf(NullSmsGatewayDriver::class);
    });

    test('the null driver reports success without making a request', function (): void {
        $response = SmsGateway::driver('null')->send([
            'message'  => 'Hello',
            'receptor' => '09123456789',
        ]);

        expect($response->status())->toBe(200)
            ->and($response->json('sent'))->toBeTrue()
            ->and($response->json('data.receptor'))->toBe('09123456789');
    });

    test('gateway() rejects a driver that does not implement the contract', function (): void {
        SmsGateway::extend('non-gateway', fn(Application $app): object => $app->make(NonGatewayDriver::class));

        SmsGateway::gateway('non-gateway');
    })->throws(LogicException::class);

    test('resolves custom drivers that do not extend the base driver', function (): void {
        SmsGateway::extend('non-gateway', function (Application $app): object {
            return $app->make(NonGatewayDriver::class);
        });

        expect(SmsGateway::driver('non-gateway'))->toBeInstanceOf(NonGatewayDriver::class);
    });
});

describe('CustomSmsGatewayDriver', function (): void {
    beforeEach(function (): void {
        SmsGateway::extend('custom', function (Application $app): SmsGatewayDriver {
            return $app->make(CustomSmsGatewayDriver::class);
        });
    });

    test('resolves as the default driver when configured', function (): void {
        config()->set('laravel-sms-gateway.default', 'custom');

        expect(SmsGateway::driver())->toBeInstanceOf(CustomSmsGatewayDriver::class);
    });

    test('sends messages', function (): void {
        Http::fake([
            'https://custom.example.com/messages' => Http::response(['ok' => true], 200),
        ]);

        $response = SmsGateway::driver('custom')->send([
            'message' => 'Hello from custom driver',
            'to'      => '09123456789',
        ]);

        expect($response->json())->toBe(['ok' => true]);

        Http::assertSent(function (Request $request): bool {
            return 'POST' === $request->method()
                && 'https://custom.example.com/messages' === $request->url()
                && 'Hello from custom driver' === $request['message'];
        });
    });

    test('dispatches SmsSent when a request receives a response', function (): void {
        Event::fake([
            SmsSent::class,
        ]);

        Http::fake([
            'https://custom.example.com/messages' => Http::response(['message_id' => 'sms-123'], 202),
        ]);

        SmsGateway::driver('custom')->request()
            ->post('messages', [
                'message' => 'Hello from request test',
                'to'      => '09123456789',
            ]);

        Event::assertDispatched(function (SmsSent $event): bool {
            return 'custom' === $event->driverName
                && 'POST' === $event->request->method()
                && 'https://custom.example.com/messages' === $event->request->url()
                && 'Hello from request test' === $event->request['message']
                && 202 === $event->response->status()
                && 'sms-123' === $event->response->json('message_id');
        });
    });

    test('applies the shared timeout defaults to requests', function (): void {
        $capturedOptions = [];

        Http::fake(function (Request $request, array $options) use (&$capturedOptions) {
            $capturedOptions = $options;

            return Http::response(['ok' => true], 200);
        });

        SmsGateway::driver('custom')->send([
            'message' => 'Hello from timeout test',
        ]);

        expect($capturedOptions)
            ->toHaveKey('timeout', 10)
            ->toHaveKey('connect_timeout', 5);
    });
});

describe('SmsGatewayDriver customization', function (): void {
    test('overrides the shared timeout defaults in configureRequest', function (): void {
        $capturedOptions = [];

        Http::fake(function (Request $request, array $options) use (&$capturedOptions) {
            $capturedOptions = $options;

            return Http::response(['ok' => true], 200);
        });

        SmsGateway::extend('configured', fn(): SmsGatewayDriver => new class extends SmsGatewayDriver {
            /**
             * @param array<string, mixed> $data
             */
            public function send(array $data): Response
            {
                return $this->request()->post('messages', $data);
            }

            protected function defaultBaseUrl(): string
            {
                return 'https://configured.example.test/';
            }

            protected function configureRequest(PendingRequest $request): PendingRequest
            {
                return $request
                    ->timeout(30)
                    ->connectTimeout(15);
            }
        });

        SmsGateway::driver('configured')->send([
            'message' => 'Hello from override test',
        ]);

        expect($capturedOptions)
            ->toHaveKey('timeout', 30)
            ->toHaveKey('connect_timeout', 15);
    });

    test('can use a different HTTP method for send', function (): void {
        Http::fake([
            'https://send.example.test/v1/messages?message=Hello%20from%20send%20test&to=09123456789' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('overridden-sendable', fn(): SmsGatewayDriver => new class extends SmsGatewayDriver {
            /**
             * @param array<string, mixed> $data
             */
            public function send(array $data): Response
            {
                return $this->request()->get('messages', $data);
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

    test('prefers the base URL configured in the driver config over the driver default', function (): void {
        config()->set('laravel-sms-gateway.default', 'overrideable');
        config()->set('laravel-sms-gateway-overrideable.api_key', 'test-api-key');
        config()->set('laravel-sms-gateway-overrideable.base_url', 'https://services-override.example.test/v1/');

        Http::fake([
            'https://services-override.example.test/v1/sms/send.json' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('overrideable', fn(): SmsGatewayDriver => new class extends SmsGatewayDriver {
            /**
             * @param array<string, mixed> $data
             */
            public function send(array $data): Response
            {
                return $this->request()->post('sms/send.json', $data);
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
});

describe('driver name resolution', function (): void {
    test('registers the same driver class under multiple names with separate config', function (): void {
        config()->set('laravel-sms-gateway-custom-a.base_url', 'https://a.example.test');
        config()->set('laravel-sms-gateway-custom-b.base_url', 'https://b.example.test');

        Http::fake([
            'https://a.example.test/messages' => Http::response(['ok' => true], 200),
            'https://b.example.test/messages' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('custom-a', fn(Application $app): SmsGatewayDriver => $app->make(CustomSmsGatewayDriver::class));
        SmsGateway::extend('custom-b', fn(Application $app): SmsGatewayDriver => $app->make(CustomSmsGatewayDriver::class));

        SmsGateway::driver('custom-a')->send(['message' => 'A']);
        SmsGateway::driver('custom-b')->send(['message' => 'B']);

        Http::assertSent(fn(Request $request): bool => 'https://a.example.test/messages' === $request->url());
        Http::assertSent(fn(Request $request): bool => 'https://b.example.test/messages' === $request->url());
    });

    test('prefers an overridden driverName() over the registration key', function (): void {
        config()->set('laravel-sms-gateway-legacy.base_url', 'https://legacy.example.test/');

        Event::fake([
            SmsSent::class,
        ]);

        Http::fake([
            'https://legacy.example.test/messages' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('modern', fn(): SmsGatewayDriver => new class extends SmsGatewayDriver {
            /**
             * @param array<string, mixed> $data
             */
            public function send(array $data): Response
            {
                return $this->request()->post('messages', $data);
            }

            protected function driverName(): string
            {
                return 'legacy';
            }

            protected function defaultBaseUrl(): string
            {
                return 'https://modern-default.example.test/';
            }
        });

        SmsGateway::driver('modern')->send(['message' => 'Hello']);

        Http::assertSent(fn(Request $request): bool => 'https://legacy.example.test/messages' === $request->url());

        Event::assertDispatched(fn(SmsSent $event): bool => 'legacy' === $event->driverName);
    });

    test('throws when a driver is used without being resolved through the manager', function (): void {
        (new CustomSmsGatewayDriver())->send(['message' => 'Hello']);
    })->throws(LogicException::class);
});

describe('shared HTTP defaults', function (): void {
    test('casts string timeout values from the environment to integers', function (): void {
        // Values set in .env always arrive as strings, but the driver reads
        // them with Config::integer(), which rejects anything else.
        $_SERVER['SMS_GATEWAY_TIMEOUT'] = '30';
        $_SERVER['SMS_GATEWAY_CONNECT_TIMEOUT'] = '15';

        try {
            $defaults = (require __DIR__ . '/../../config/laravel-sms-gateway.php')['defaults'];
        } finally {
            unset($_SERVER['SMS_GATEWAY_TIMEOUT'], $_SERVER['SMS_GATEWAY_CONNECT_TIMEOUT']);
        }

        expect($defaults['timeout'])->toBe(30)
            ->and($defaults['connect_timeout'])->toBe(15);
    });
});
