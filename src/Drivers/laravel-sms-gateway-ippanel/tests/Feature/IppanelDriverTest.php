<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('ippanel driver sends credentials as query parameters', function (): void {
    config()->set('laravel-sms-gateway.default', 'ippanel');
    config()->set('laravel-sms-gateway-ippanel.username', 'ippanel-username');
    config()->set('laravel-sms-gateway-ippanel.password', 'ippanel-password');

    $response = ['status' => 'ok'];

    Http::fake([
        'https://ippanel.com/services.jspd*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send([
        'op'      => 'send',
        'from'    => '3000505',
        'to'      => '09123456789',
        'message' => 'Hello from IPPanel',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://ippanel.com/services.jspd' === mb_rtrim((string) strtok($request->url(), '?'), '/')
            && 'ippanel-username' === $query['uname']
            && 'ippanel-password' === $query['pass']
            && $request->isForm()
            && 'send' === $request['op']
            && '3000505' === $request['from']
            && '09123456789' === $request['to']
            && 'Hello from IPPanel' === $request['message'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('laravel-sms-gateway.default', 'ippanel');
    config()->set('laravel-sms-gateway-ippanel.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 'ok'], 200),
    ]);

    SmsGateway::driver()->send([
        'message' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/services.jspd' === strtok($request->url(), '?');
    });
});
