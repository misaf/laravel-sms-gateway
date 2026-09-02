<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send simple SMS via Ghasedak driver', function (): void {
    config()->set('sms-gateway.default', 'ghasedak');
    config()->set('sms-gateway-ghasedak.api_key', 'ghasedak-api-key');

    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple' => Http::response($response, Response::HTTP_OK),
    ]);

    $result = SmsGateway::driver()->send([
        'message'  => 'Here is a test message, as described in the documentation.',
        'receptor' => '+989119632587',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.ghasedak.me/v2/sms/send/simple' === $request->url()
            && $request->hasHeader('apikey', 'ghasedak-api-key')
            && 'Here is a test message, as described in the documentation.' === $request['message']
            && '+989119632587' === $request['receptor'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'ghasedak');
    config()->set('sms-gateway-ghasedak.api_key', 'ghasedak-api-key');
    config()->set('sms-gateway-ghasedak.base_url', 'https://services-override.example.test/v2/');

    Http::fake([
        'https://services-override.example.test/v2/sms/send/simple' => Http::response(['result' => ['code' => 200]], Response::HTTP_OK),
    ]);

    SmsGateway::driver()->send([
        'message'  => 'Hello',
        'receptor' => '+989119632587',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/v2/sms/send/simple' === $request->url()
            && $request->hasHeader('apikey', 'ghasedak-api-key');
    });
});

test('rejects a configured but empty API key', function (): void {
    config()->set('sms-gateway-ghasedak.api_key', '');

    expect(fn() => SmsGateway::driver('ghasedak'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Ghasedak API key is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});
