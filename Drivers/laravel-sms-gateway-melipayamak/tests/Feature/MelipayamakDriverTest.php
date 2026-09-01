<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('melipayamak driver sends credentials as query parameters', function (): void {
    config()->set('sms-gateway.default', 'melipayamak');
    config()->set('sms-gateway-melipayamak.username', 'melipayamak-username');
    config()->set('sms-gateway-melipayamak.password', 'melipayamak-password');

    $response = ['Value' => '123456789'];

    Http::fake([
        'https://rest.payamak-panel.com/api/*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send([
        'to'   => '09123456789',
        'from' => '50004000',
        'text' => 'Hello from Melipayamak',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://rest.payamak-panel.com/api/SendSMS/SendSMS' === strtok($request->url(), '?')
            && 'melipayamak-username' === $query['username']
            && 'melipayamak-password' === $query['password']
            && $request->isForm()
            && '09123456789' === $request['to']
            && '50004000' === $request['from']
            && 'Hello from Melipayamak' === $request['text'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'melipayamak');
    config()->set('sms-gateway-melipayamak.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['Value' => '1'], 200),
    ]);

    SmsGateway::driver()->send([
        'text' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/SendSMS/SendSMS' === strtok($request->url(), '?');
    });
});
