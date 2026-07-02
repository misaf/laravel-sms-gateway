<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send SMS via Magfa driver', function (): void {
    config()->set('sms_gateway.default', 'magfa');
    config()->set('services.magfa.username', 'magfa-username');
    config()->set('services.magfa.password', 'magfa-password');

    $response = ['status' => 0, 'messages' => [['id' => 123]]];

    Http::fake([
        'https://sms.magfa.com/api/http/sms/v2/send' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('send', [
            'senders'    => ['3000'],
            'recipients' => ['09123456789'],
            'messages'   => ['Hello from Magfa'],
        ])
        ->json();

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
