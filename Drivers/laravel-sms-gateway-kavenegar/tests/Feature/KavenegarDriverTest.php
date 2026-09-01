<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send request through kavenegar driver', function (): void {
    config()->set('sms-gateway.default', 'kavenegar');
    config()->set('sms-gateway-kavenegar.api_key', 'test-api-key');

    Http::fake([
        'https://api.kavenegar.com/v1/test-api-key/sms/send.json' => Http::response([
            'return' => ['status' => 200, 'message' => 'success'],
        ], 200),
    ]);

    $response = SmsGateway::driver()->send([
        'receptor' => '09123456789',
        'message'  => 'Hello from kavenegar',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.kavenegar.com/v1/test-api-key/sms/send.json' === $request->url()
            && $request->isForm()
            && '09123456789' === $request['receptor']
            && 'Hello from kavenegar' === $request['message'];
    });

    expect($response['return']['status'])->toBe(200);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'kavenegar');
    config()->set('sms-gateway-kavenegar.base_url', 'https://services-override.example.test/v1/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['return' => ['status' => 200]], 200),
    ]);

    SmsGateway::driver()->send([
        'receptor' => '09123456789',
        'message'  => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/v1/test-api-key/sms/send.json' === $request->url();
    });
});

test('rejects a configured but empty API key', function (): void {
    config()->set('sms-gateway-kavenegar.api_key', '');

    expect(fn() => SmsGateway::driver('kavenegar'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Kavenegar API key is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});
