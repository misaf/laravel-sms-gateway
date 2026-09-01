<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Misaf\LaravelSmsGateway\Events\SmsSendFailed;
use Misaf\LaravelSmsGateway\Events\SmsSending;
use Misaf\LaravelSmsGateway\Events\SmsSendUnreachable;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers\BaseSmsGatewayDriver;

describe('the shared driver base', function (): void {
    test('sends through the configured base URL and credentials', function (): void {
        Http::fake([
            'https://base.example.com/messages' => Http::response(['id' => 'sms-1']),
        ]);

        $response = (new BaseSmsGatewayDriver())->send(['message' => 'Hello']);

        expect($response->json('id'))->toBe('sms-1');
        Http::assertSent(function (Request $request): bool {
            return 'POST' === $request->method()
                && 'https://base.example.com/messages' === $request->url()
                && 'secret' === $request->header('X-Api-Key')[0]
                && 'Hello' === $request['message'];
        });
    });

    test('prefers the configured base URL over the driver default', function (): void {
        Http::fake([
            'https://override.example.com/messages' => Http::response(),
        ]);

        (new BaseSmsGatewayDriver('https://override.example.com/'))->send(['message' => 'Hello']);

        Http::assertSent(fn(Request $request): bool => 'https://override.example.com/messages' === $request->url());
    });

    test('dispatches SmsSending and then SmsSent on a successful response', function (): void {
        Event::fake([SmsSending::class, SmsSent::class, SmsSendFailed::class]);

        Http::fake([
            'https://base.example.com/messages' => Http::response(['id' => 'sms-1'], 202),
        ]);

        (new BaseSmsGatewayDriver())->send(['message' => 'Hello']);

        Event::assertDispatched(fn(SmsSending $event): bool => 'base' === $event->driverName
            && 'Hello' === $event->data['message']);
        Event::assertDispatched(fn(SmsSent $event): bool => 'base' === $event->driverName
            && 202 === $event->response->status()
            && 'sms-1' === $event->response->json('id'));
        Event::assertNotDispatched(SmsSendFailed::class);
    });

    test('dispatches SmsSendFailed with the response when the gateway rejects the send', function (): void {
        Event::fake([SmsSent::class, SmsSendFailed::class]);

        Http::fake([
            'https://base.example.com/messages' => Http::response(['error' => 'invalid'], 422),
        ]);

        $response = (new BaseSmsGatewayDriver())->send(['message' => 'Hello']);

        expect($response->status())->toBe(422);
        Event::assertNotDispatched(SmsSent::class);
        Event::assertDispatched(fn(SmsSendFailed $event): bool => 'base' === $event->driverName
            && 422 === $event->response->status()
            && 'invalid' === $event->response->json('error')
            && 'https://base.example.com/messages' === $event->request->url());
    });

    test('dispatches SmsSendUnreachable when the gateway is never reached', function (): void {
        Event::fake([SmsSent::class, SmsSendFailed::class, SmsSendUnreachable::class]);

        Http::fake([
            'https://base.example.com/messages' => fn(): never => throw new ConnectionException('Connection timed out'),
        ]);

        expect(fn() => (new BaseSmsGatewayDriver())->send(['message' => 'Hello']))
            ->toThrow(ConnectionException::class);

        Event::assertNotDispatched(SmsSent::class);
        Event::assertNotDispatched(SmsSendFailed::class);
        Event::assertDispatched(fn(SmsSendUnreachable $event): bool => 'base' === $event->driverName
            && $event->exception instanceof ConnectionException);
    });

    test('retries gateway 5xx responses but not client errors', function (): void {
        Http::fake([
            'https://base.example.com/messages' => Http::sequence()
                ->push(['error' => 'boom'], 500)
                ->push(['id' => 'sms-1'], 200),
        ]);

        $response = (new BaseSmsGatewayDriver(retryTimes: 2))->send(['message' => 'Hello']);

        expect($response->json('id'))->toBe('sms-1');
        Http::assertSentCount(2);
    });

    test('rejects an empty base URL', function (): void {
        expect(fn(): BaseSmsGatewayDriver => new BaseSmsGatewayDriver(''))
            ->toThrow(
                InvalidArgumentException::class,
                "The base URL is empty. Set it in the driver's config file, or in the matching environment variable."
            );
    });

    test('rejects an empty credential guarded by the driver', function (): void {
        expect(fn(): BaseSmsGatewayDriver => new BaseSmsGatewayDriver(apiKey: ''))
            ->toThrow(
                InvalidArgumentException::class,
                "The API key is empty. Set it in the driver's config file, or in the matching environment variable."
            );
    });
});
