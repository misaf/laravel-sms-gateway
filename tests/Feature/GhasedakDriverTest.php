<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send simple SMS via Ghasedak driver', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver('ghasedak')->send()
        ->post('sms/send/simple', [
            'message'  => 'Here is a test message, as described in the documentation.',
            'receptor' => '+989119632587',
        ])
        ->json();

    expect($result)->toEqual($response);
});

test('can send simple SMS with line number via Ghasedak driver', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver('ghasedak')->send()
        ->post('sms/send/simple', [
            'message'    => 'Here is a test message, as described in the documentation.',
            'receptor'   => '+989119632587',
            'linenumber' => config('sms_gateway.drivers.ghasedak.linenumber'),
        ])
        ->json();

    expect($result)->toEqual($response);
});

test('can get account info via Ghasedak driver', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => ['remain' => 1000]];

    Http::fake([
        'https://api.ghasedak.me/v2/account/info' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver('ghasedak')->send()
        ->get('account/info')
        ->json();

    expect($result)->toEqual($response);
});

test('can detect invalid apiKey via Ghasedak driver', function (): void {
    $response = ['result' => ['code' => 401, 'message' => 'apiKey is invalid'], 'items' => null];

    Http::fake([
        'https://api.ghasedak.me/v2/account/info' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver('ghasedak')->send()
        ->get('account/info')
        ->json();

    expect($result)->toEqual($response);
    expect($result['result']['code'])->toBe(401);
});
