<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('sunway driver sends credentials as query parameters', function (): void {
    config()->set('sms-gateway.default', 'sunway');
    config()->set('sms-gateway-sunway.username', 'username');
    config()->set('sms-gateway-sunway.password', 'password');

    Http::fake([
        'https://sms.sunwaysms.com/smsws/HttpService.ashx*' => Http::response(['status' => 'ok'], 200),
    ]);

    $result = SmsGateway::driver()->send([
        'method'  => 'SendSMS',
        'mobile'  => '09123456789',
        'message' => 'Hello, World!',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'GET' === $request->method()
            && 'username' === $query['UserName']
            && 'password' === $query['Password']
            && 'SendSMS' === $query['method']
            && '09123456789' === $query['mobile']
            && 'Hello, World!' === $query['message'];
    });

    expect($result)->toBe(['status' => 'ok']);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'sunway');
    config()->set('sms-gateway-sunway.base_url', 'https://services-override.example.test/smsws/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 'ok'], 200),
    ]);

    SmsGateway::driver()->send([
        'message' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/smsws/HttpService.ashx' === strtok($request->url(), '?');
    });
});
