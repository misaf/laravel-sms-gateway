<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send SMS via Twilio driver', function (): void {
    config()->set('sms_gateway.default', 'twilio');
    config()->set('services.twilio.account_sid', 'AC123');
    config()->set('services.twilio.auth_token', 'twilio-auth-token');

    $response = ['sid' => 'SM123', 'status' => 'queued'];

    Http::fake([
        'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages.json' => Http::response($response, 201),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('Messages.json', [
            'To'   => '+15005550006',
            'From' => '+15005550001',
            'Body' => 'Here is a test message.',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages.json' === $request->url()
            && $request->hasHeader('Authorization', 'Basic ' . base64_encode('AC123:twilio-auth-token'))
            && $request->isForm()
            && '+15005550006' === $request['To']
            && '+15005550001' === $request['From']
            && 'Here is a test message.' === $request['Body'];
    });

    expect($result)->toEqual($response);
});

test('twilio driver scopes the default gateway to the configured account', function (): void {
    config()->set('services.twilio.account_sid', 'AC456');
    config()->set('services.twilio.auth_token', 'twilio-auth-token');

    Http::fake([
        'https://api.twilio.com/2010-04-01/Accounts/AC456/*' => Http::response(['ok' => true], 200),
    ]);

    SmsGateway::driver('twilio')->send()->get('Messages.json');

    Http::assertSent(function (Request $request): bool {
        return 'https://api.twilio.com/2010-04-01/Accounts/AC456/Messages.json' === $request->url();
    });
});
