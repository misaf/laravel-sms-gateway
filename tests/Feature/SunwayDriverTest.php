<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('sunway driver sends credentials as query parameters', function (): void {
    config()->set('sms_gateway.default', 'sunway');
    config()->set('sms_gateway.drivers.sunway.gateway', 'https://sms.sunwaysms.com/smsws/HttpService.ashx');
    config()->set('services.sunway.username', 'username');
    config()->set('services.sunway.password', 'password');

    Http::fake([
        'https://sms.sunwaysms.com/smsws/HttpService.ashx*' => Http::response(['status' => 'ok'], 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->get('', [
            'method' => 'SendSMS',
            'mobile' => '09123456789',
            'message' => 'Hello, World!',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return $request->method() === 'GET'
            && $query['UserName'] === 'username'
            && $query['Password'] === 'password'
            && $query['method'] === 'SendSMS'
            && $query['mobile'] === '09123456789'
            && $query['message'] === 'Hello, World!';
    });

    expect($result)->toBe(['status' => 'ok']);
});
