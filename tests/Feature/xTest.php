<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('check driver', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple/' => Http::response($response, 200),
    ]);

    expect(SmsGateway::driver('ghasedak')->sendSimpleSms(
        message: 'Here is a test message, as described in the documentation.',
        receptor: '+989119632587',
    ))->toEqual($response);
})->only();

test('check if lineNumber is null', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple/' => Http::response($response, 200),
    ]);

    expect(SmsGateway::driver('ghasedak')->sendSimpleSms(
        message: 'Here is a test message, as described in the documentation.',
        receptor: '+989119632587',
    ))->toEqual($response);
});

test('can detect apiKey is invalid', function (): void {
    $response = ['result' => ['code' => 401, 'message' => 'apiKey is invalid'], 'items' => null];

    Http::fake([
        'https://api.ghasedak.me/v2/account/info/' => Http::response($response, 200),
    ]);

    expect(SmsGateway::driver('ghasedak')->accountInfo())->toEqual($response);
});

test('can detect apiKey is invali22d', function (): void {
    $response = ['result' => ['code' => 200, 'message' => 'success'], 'items' => '2578793735'];

    Http::fake([
        'https://api.ghasedak.me/v2/sms/send/simple/' => Http::response($response, 200),
    ]);

    expect(SmsGateway::driver('ghasedak')->sendSmsSimple(
        message: 'test message',
        receptor: '09123123123',
        linenumber: '123',
        senddate: now()->timestamp,
    ))->toEqual($response);
});
