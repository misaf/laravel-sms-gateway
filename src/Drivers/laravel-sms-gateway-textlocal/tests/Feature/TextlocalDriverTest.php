<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send SMS via Textlocal driver', function (): void {
    config()->set('laravel-sms-gateway.default', 'textlocal');
    config()->set('laravel-sms-gateway-textlocal.api_key', 'textlocal-api-key');

    $response = ['status' => 'success'];

    Http::fake([
        'https://api.txtlocal.com/*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send([
        'numbers' => '447123456789',
        'sender'  => 'Laravel',
        'message' => 'Hello from Textlocal',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://api.txtlocal.com/send/?apikey=textlocal-api-key' === $request->url()
            && 'textlocal-api-key' === $query['apikey']
            && $request->isForm()
            && '447123456789' === $request['numbers']
            && 'Laravel' === $request['sender']
            && 'Hello from Textlocal' === $request['message'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('laravel-sms-gateway.default', 'textlocal');
    config()->set('laravel-sms-gateway-textlocal.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 'success'], 200),
    ]);

    SmsGateway::driver()->send([
        'message' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/send/' === strtok($request->url(), '?');
    });
});
