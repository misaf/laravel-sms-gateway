<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send SMS via Vonage driver', function (): void {
    config()->set('sms-gateway.default', 'vonage');
    config()->set('sms-gateway-vonage.api_key', 'vonage-api-key');
    config()->set('sms-gateway-vonage.api_secret', 'vonage-api-secret');

    $response = ['message-count' => '1', 'messages' => [['status' => '0']]];

    Http::fake([
        'https://rest.nexmo.com/*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send([
        'from' => 'Laravel',
        'to'   => '14155550100',
        'text' => 'Hello from Vonage',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://rest.nexmo.com/sms/json' === strtok($request->url(), '?')
            && 'vonage-api-key' === $query['api_key']
            && 'vonage-api-secret' === $query['api_secret']
            && $request->isForm()
            && 'Laravel' === $request['from']
            && '14155550100' === $request['to']
            && 'Hello from Vonage' === $request['text'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'vonage');
    config()->set('sms-gateway-vonage.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['message-count' => '1'], 200),
    ]);

    SmsGateway::driver()->send([
        'text' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/sms/json' === strtok($request->url(), '?');
    });
});

test('rejects a configured but empty API key', function (): void {
    config()->set('sms-gateway-vonage.api_key', '');

    expect(fn() => SmsGateway::driver('vonage'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Vonage API key is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});

test('rejects a configured but empty API secret', function (): void {
    config()->set('sms-gateway-vonage.api_secret', '');

    expect(fn() => SmsGateway::driver('vonage'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Vonage API secret is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});
