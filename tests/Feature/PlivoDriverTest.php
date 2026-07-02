<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send SMS via Plivo driver', function (): void {
    config()->set('sms_gateway.default', 'plivo');
    config()->set('services.plivo.auth_id', 'MA123');
    config()->set('services.plivo.auth_token', 'plivo-auth-token');

    $response = ['message_uuid' => ['uuid'], 'api_id' => 'api-id'];

    Http::fake([
        'https://api.plivo.com/v1/Account/MA123/Message/' => Http::response($response, 202),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('Message/', [
            'src'  => '14151234567',
            'dst'  => '14157654321',
            'text' => 'Hello from Plivo',
        ])
        ->json();

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
