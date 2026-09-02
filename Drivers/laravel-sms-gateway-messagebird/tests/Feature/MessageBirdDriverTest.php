<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Facades\SmsGateway;

test('can send SMS via MessageBird driver', function (): void {
    config()->set('sms-gateway.default', 'messagebird');
    config()->set('sms-gateway-messagebird.access_key', 'messagebird-access-key');

    $response = ['id' => 'message-id', 'status' => 'sent'];

    Http::fake([
        'https://rest.messagebird.com/messages' => Http::response($response, Response::HTTP_CREATED),
    ]);

    $result = SmsGateway::driver()->send([
        'originator' => 'Laravel',
        'recipients' => ['31612345678'],
        'body'       => 'Hello from MessageBird',
    ])->json();

    Http::assertSent(function (Request $request): bool {
        return 'https://rest.messagebird.com/messages' === $request->url()
            && $request->hasHeader('Authorization', 'AccessKey messagebird-access-key')
            && $request->isForm()
            && 'Laravel' === $request['originator']
            && ['31612345678'] === $request['recipients']
            && 'Hello from MessageBird' === $request['body'];
    });

    expect($result)->toEqual($response);
});

test('prefers the base URL configured in the driver config over the driver default', function (): void {
    config()->set('sms-gateway.default', 'messagebird');
    config()->set('sms-gateway-messagebird.base_url', 'https://services-override.example.test/');

    Http::fake([
        'https://services-override.example.test/*' => Http::response(['status' => 'sent'], Response::HTTP_CREATED),
    ]);

    SmsGateway::driver()->send([
        'body' => 'Hello',
    ]);

    Http::assertSent(function (Request $request): bool {
        return 'https://services-override.example.test/messages' === $request->url();
    });
});

test('rejects a configured but empty access key', function (): void {
    config()->set('sms-gateway-messagebird.access_key', '');

    expect(fn() => SmsGateway::driver('messagebird'))
        ->toThrow(
            InvalidArgumentException::class,
            "The MessageBird access key is empty. Set it in the driver's config file, or in the matching environment variable."
        );
});
