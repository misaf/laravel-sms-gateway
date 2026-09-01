<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send request through smsir driver', function (): void {
    config()->set('sms-gateway.default', 'smsir');
    config()->set('sms-gateway-smsir.api_key', 'smsir-api-key');

    Http::fake([
        'https://api.sms.ir/v1/send/bulk' => Http::response([
            'status'  => 1,
            'message' => 'ok',
        ], 200),
    ]);

    $response = SmsGateway::driver()->send([
        'mobile'  => '09123456789',
        'message' => 'Hello from sms.ir',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.sms.ir/v1/send/bulk' === $request->url()
            && $request->hasHeader('X-API-KEY', 'smsir-api-key')
            && '09123456789' === $request['mobile']
            && 'Hello from sms.ir' === $request['message'];
    });

    expect($response['status'])->toBe(1);
});

test('does not send api key header when smsir api key is missing', function (): void {
    config()->set('sms-gateway.default', 'smsir');

    Http::fake([
        'https://api.sms.ir/v1/send/bulk' => Http::response([
            'status'  => 1,
            'message' => 'ok',
        ], 200),
    ]);

    SmsGateway::driver()->send([
        'mobile'  => '09123456789',
        'message' => 'Hello from sms.ir',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://api.sms.ir/v1/send/bulk' === $request->url()
            && ! $request->hasHeader('X-API-KEY');
    });
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'smsir');
    config()->set('sms-gateway-smsir.base_url', 'https://services-override.example.test/v1/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 1], 200),
    ]);

    SmsGateway::driver()->send([
        'message' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/v1/send/bulk' === $request->url();
    });
});
