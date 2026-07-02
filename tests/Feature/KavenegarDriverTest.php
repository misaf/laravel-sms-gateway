<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send request through kavenegar driver', function (): void {
    config()->set('sms_gateway.default', 'kavenegar');
    config()->set('services.kavenegar.api_key', 'test-api-key');

    Http::fake([
        'https://api.kavenegar.com/v1/sms/send.json' => Http::response([
            'return' => ['status' => 200, 'message' => 'success'],
        ], 200),
    ]);

    $response = SmsGateway::driver()->send()
        ->post('sms/send.json', [
            'receptor' => '09123456789',
            'message'  => 'Hello from kavenegar',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.kavenegar.com/v1/sms/send.json' === $request->url()
            && $request->hasHeader('apikey', 'test-api-key')
            && '09123456789' === $request['receptor']
            && 'Hello from kavenegar' === $request['message'];
    });

    expect($response['return']['status'])->toBe(200);
});
