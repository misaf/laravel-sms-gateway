<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send simple SMS via Ghasedak driver', function (): void {
    config()->set('sms_gateway.default', 'ghasedak');
    config()->set('services.ghasedak.api_key', 'ghasedak-api-key');

    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('sms/send/simple', [
            'message'  => 'Here is a test message, as described in the documentation.',
            'receptor' => '+989119632587',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.ghasedak.me/v2/sms/send/simple' === $request->url()
            && $request->hasHeader('apikey', 'ghasedak-api-key')
            && 'Here is a test message, as described in the documentation.' === $request['message']
            && '+989119632587' === $request['receptor'];
    });

    expect($result)->toEqual($response);
});

test('can send simple SMS with line number via Ghasedak driver', function (): void {
    config()->set('sms_gateway.default', 'ghasedak');
    config()->set('services.ghasedak.line_number', '300050040007');

    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('sms/send/simple', [
            'message'    => 'Here is a test message, as described in the documentation.',
            'receptor'   => '+989119632587',
            'linenumber' => config('services.ghasedak.line_number'),
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.ghasedak.me/v2/sms/send/simple' === $request->url()
            && 'Here is a test message, as described in the documentation.' === $request['message']
            && '+989119632587' === $request['receptor']
            && '300050040007' === $request['linenumber'];
    });

    expect($result)->toEqual($response);
});

test('can get account info via Ghasedak driver', function (): void {
    config()->set('sms_gateway.default', 'ghasedak');

    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => ['remain' => 1000]];

    Http::fake([
        'https://api.ghasedak.me/v2/account/info' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->get('account/info')
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'GET' === $request->method()
            && 'https://api.ghasedak.me/v2/account/info' === $request->url();
    });

    expect($result)->toEqual($response);
});
