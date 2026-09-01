<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send SMS via Plivo driver', function (): void {
    config()->set('sms-gateway.default', 'plivo');
    config()->set('sms-gateway-plivo.auth_id', 'MA123');
    config()->set('sms-gateway-plivo.auth_token', 'plivo-auth-token');

    $response = ['message_uuid' => ['uuid'], 'api_id' => 'api-id'];

    Http::fake([
        'https://api.plivo.com/v1/Account/MA123/Message/' => Http::response($response, 202),
    ]);

    $result = SmsGateway::driver()->send([
        'src'  => '14151234567',
        'dst'  => '14157654321',
        'text' => 'Hello from Plivo',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.plivo.com/v1/Account/MA123/Message/' === $request->url()
            && $request->hasHeader('Authorization', 'Basic ' . base64_encode('MA123:plivo-auth-token'))
            && $request->isJson()
            && '14151234567' === $request['src']
            && '14157654321' === $request['dst']
            && 'Hello from Plivo' === $request['text'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'plivo');
    config()->set('sms-gateway-plivo.auth_id', 'MA123');
    config()->set('sms-gateway-plivo.base_url', 'https://services-override.example.test/v1/Account/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['api_id' => 'api-id'], 202),
    ]);

    SmsGateway::driver()->send([
        'text' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/v1/Account/MA123/Message/' === $request->url();
    });
});
