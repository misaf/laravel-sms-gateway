<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('sunway driver sends credentials as query parameters', function (): void {
    config()->set('sms_gateway.default', 'sunway');
    config()->set('services.sunway.username', 'username');
    config()->set('services.sunway.password', 'password');

    Http::fake([
        'https://sms.sunwaysms.com/smsws/HttpService.ashx*' => Http::response(['status' => 'ok'], 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->get('', [
            'method'  => 'SendSMS',
            'mobile'  => '09123456789',
            'message' => 'Hello, World!',
        ])
        ->json();

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
