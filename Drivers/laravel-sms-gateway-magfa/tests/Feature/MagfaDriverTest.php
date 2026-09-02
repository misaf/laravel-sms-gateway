<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send SMS via Magfa driver', function (): void {
    config()->set('sms-gateway.default', 'magfa');
    config()->set('sms-gateway-magfa.username', 'magfa-username');
    config()->set('sms-gateway-magfa.password', 'magfa-password');

    $response = ['status' => 0, 'messages' => [['id' => 123]]];

    Http::fake([
        'https://sms.magfa.com/api/http/sms/v2/send' => Http::response($response, Response::HTTP_OK),
    ]);

    $result = SmsGateway::driver()->send([
        'senders'    => ['3000'],
        'recipients' => ['09123456789'],
        'messages'   => ['Hello from Magfa'],
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://sms.magfa.com/api/http/sms/v2/send' === $request->url()
            && $request->hasHeader('Authorization', 'Basic ' . base64_encode('magfa-username:magfa-password'))
            && $request->isJson()
            && ['3000'] === $request['senders']
            && ['09123456789'] === $request['recipients']
            && ['Hello from Magfa'] === $request['messages'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'magfa');
    config()->set('sms-gateway-magfa.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 0], Response::HTTP_OK),
    ]);

    SmsGateway::driver()->send([
        'messages' => ['Hello'],
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/send' === $request->url();
    });
});

test('rejects a configured but empty username', function (): void {
    config()->set('sms-gateway-magfa.username', '');

    expect(fn() => SmsGateway::driver('magfa'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Magfa username is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});

test('rejects a configured but empty password', function (): void {
    config()->set('sms-gateway-magfa.password', '');

    expect(fn() => SmsGateway::driver('magfa'))
        ->toThrow(
            InvalidArgumentException::class,
            "The Magfa password is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});
