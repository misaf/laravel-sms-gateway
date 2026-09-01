<?php

declare(strict_types=1);

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway as SmsGatewayContract;
use Misaf\LaravelSmsGateway\Drivers\NullSmsGatewayDriver;
use Misaf\LaravelSmsGateway\Events\SmsSending;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\CustomSmsGatewayDriver;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\NonGatewayDriver;

describe('driver resolution', function (): void {
    test('reads the default driver name from config', function (): void {
        config()->set('sms-gateway.default', 'sunway');

        expect(SmsGateway::getDefaultDriver())->toBe('sunway');
    });

    test('falls back to the null driver when none is configured', function (): void {
        config()->set('sms-gateway', [
            'defaults' => config('sms-gateway.defaults'),
        ]);

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

    test('the null driver dispatches the lifecycle events', function (): void {
        Event::fake([SmsSending::class, SmsSent::class]);

        SmsGateway::driver('null')->send([
            'message'  => 'Hello',
            'receptor' => '09123456789',
        ]);

        Event::assertDispatched(SmsSending::class, function (SmsSending $event): bool {
            return 'null' === $event->driverName
                && '09123456789' === $event->data['receptor'];
        });

        Event::assertDispatched(SmsSent::class, function (SmsSent $event): bool {
            return 'null' === $event->driverName
                && $event->response->json('sent')
                && 'Hello' === $event->request['message'];
        });
    });

    test('resolves custom drivers that do not implement the gateway contract', function (): void {
        SmsGateway::extend('non-gateway', function (Application $app): object {
            return $app->make(NonGatewayDriver::class);
        });

        expect(SmsGateway::driver('non-gateway'))->toBeInstanceOf(NonGatewayDriver::class);
    });
});

describe('CustomSmsGatewayDriver', function (): void {
    beforeEach(function (): void {
        SmsGateway::extend('custom', fn(): SmsGatewayContract => new CustomSmsGatewayDriver());
    });

    test('resolves as the default driver when configured', function (): void {
        config()->set('sms-gateway.default', 'custom');

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

    test('applies the timeouts it was constructed with', function (): void {
        $capturedOptions = [];

        Http::fake(function (Request $request, array $options) use (&$capturedOptions) {
            $capturedOptions = $options;

            return Http::response(['ok' => true], 200);
        });

        SmsGateway::driver('custom')->send([
            'message' => 'Hello from timeout test',
        ]);

        expect($capturedOptions)
            ->toHaveKey('timeout', 6)
            ->toHaveKey('connect_timeout', 5);
    });
});

describe('driver construction', function (): void {
    test('a driver uses the timeouts passed by its service provider', function (): void {
        $capturedOptions = [];

        Http::fake(function (Request $request, array $options) use (&$capturedOptions) {
            $capturedOptions = $options;

            return Http::response(['ok' => true], 200);
        });

        SmsGateway::extend('configured', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            serverTimeout: 15,
            clientTimeout: 30,
        ));

        SmsGateway::driver('configured')->send([
            'message' => 'Hello from override test',
        ]);

        expect($capturedOptions)
            ->toHaveKey('timeout', 30)
            ->toHaveKey('connect_timeout', 15);
    });

    test('a configured base URL wins over the driver default', function (): void {
        config()->set('sms-gateway.default', 'overrideable');

        Http::fake([
            'https://services-override.example.test/v1/messages' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('overrideable', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            apiKey: 'test-api-key',
            baseUrl: 'https://services-override.example.test/v1/',
        ));

        SmsGateway::driver()->send([
            'receptor' => '09123456789',
            'message'  => 'Hello',
        ]);

        Http::assertSent(function (Request $request): bool {
            return 'https://services-override.example.test/v1/messages' === $request->url()
                && $request->hasHeader('apikey', 'test-api-key');
        });
    });

    test('registers the same driver class under multiple names with separate config', function (): void {
        Http::fake([
            'https://a.example.test/messages' => Http::response(['ok' => true], 200),
            'https://b.example.test/messages' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('custom-a', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            baseUrl: 'https://a.example.test',
            driverName: 'custom-a',
        ));
        SmsGateway::extend('custom-b', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            baseUrl: 'https://b.example.test',
            driverName: 'custom-b',
        ));

        SmsGateway::driver('custom-a')->send(['message' => 'A']);
        SmsGateway::driver('custom-b')->send(['message' => 'B']);

        Http::assertSent(fn(Request $request): bool => 'https://a.example.test/messages' === $request->url());
        Http::assertSent(fn(Request $request): bool => 'https://b.example.test/messages' === $request->url());
    });

    test('dispatches SmsSent with the name the driver was built with', function (): void {
        Event::fake([
            SmsSent::class,
        ]);

        Http::fake([
            'https://legacy.example.test/messages' => Http::response(['ok' => true], 200),
        ]);

        SmsGateway::extend('modern', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            baseUrl: 'https://legacy.example.test/',
            driverName: 'legacy',
        ));

        SmsGateway::driver('modern')->send(['message' => 'Hello']);

        Http::assertSent(fn(Request $request): bool => 'https://legacy.example.test/messages' === $request->url());

        Event::assertDispatched(fn(SmsSent $event): bool => 'legacy' === $event->driverName);
    });
});

describe('retry policy', function (): void {
    beforeEach(function (): void {
        SmsGateway::extend('custom', fn(): SmsGatewayContract => new CustomSmsGatewayDriver(
            retrySleepMilliseconds: 0,
        ));
    });

    test('retries a gateway server error and returns the eventual success', function (): void {
        Http::fake([
            'https://custom.example.com/*' => Http::sequence()
                ->push(['error' => 'busy'], 500)
                ->push(['ok' => true], 200),
        ]);

        $response = SmsGateway::driver('custom')->send(['message' => 'Hello']);

        expect($response->status())->toBe(200);
        Http::assertSentCount(2);
    });

    test('does not retry a client error such as a rejected credential', function (): void {
        Http::fake([
            'https://custom.example.com/*' => Http::response(['error' => 'unauthorized'], 401),
        ]);

        $response = SmsGateway::driver('custom')->send(['message' => 'Hello']);

        expect($response->status())->toBe(401);
        Http::assertSentCount(1);
    });
});

describe('shared HTTP defaults', function (): void {
    test('casts string timeout and retry values from the environment to integers', function (): void {
        $_SERVER['SMS_GATEWAY_SERVER_TIMEOUT'] = '15';
        $_SERVER['SMS_GATEWAY_CLIENT_TIMEOUT'] = '30';
        $_SERVER['SMS_GATEWAY_RETRY_TIMES'] = '4';
        $_SERVER['SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS'] = '250';

        try {
            $defaults = (require __DIR__ . '/../../config/sms-gateway.php')['defaults'];
        } finally {
            unset(
                $_SERVER['SMS_GATEWAY_SERVER_TIMEOUT'],
                $_SERVER['SMS_GATEWAY_CLIENT_TIMEOUT'],
                $_SERVER['SMS_GATEWAY_RETRY_TIMES'],
                $_SERVER['SMS_GATEWAY_RETRY_SLEEP_MILLISECONDS'],
            );
        }

        expect($defaults['server_timeout'])->toBe(15)
            ->and($defaults['client_timeout'])->toBe(30)
            ->and($defaults['retry_times'])->toBe(4)
            ->and($defaults['retry_sleep_milliseconds'])->toBe(250);
    });
});
