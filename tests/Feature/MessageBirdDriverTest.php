<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send SMS via MessageBird driver', function (): void {
    config()->set('sms_gateway.default', 'messagebird');
    config()->set('services.messagebird.access_key', 'messagebird-access-key');

    $response = ['id' => 'message-id', 'status' => 'sent'];

    Http::fake([
        'https://rest.messagebird.com/messages' => Http::response($response, 201),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('messages', [
            'originator' => 'Laravel',
            'recipients' => ['31612345678'],
            'body'       => 'Hello from MessageBird',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://rest.messagebird.com/messages' === $request->url()
            && $request->hasHeader('Authorization', 'AccessKey messagebird-access-key')
            && $request->isJson()
            && 'Laravel' === $request['originator']
            && ['31612345678'] === $request['recipients']
            && 'Hello from MessageBird' === $request['body'];
    });

    expect($result)->toEqual($response);
});
