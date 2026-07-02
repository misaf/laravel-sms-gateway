<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('can send SMS via Textlocal driver', function (): void {
    config()->set('sms_gateway.default', 'textlocal');
    config()->set('services.textlocal.api_key', 'textlocal-api-key');

    $response = ['status' => 'success'];

    Http::fake([
        'https://api.txtlocal.com/send*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('send', [
            'numbers' => '447123456789',
            'sender'  => 'Laravel',
            'message' => 'Hello from Textlocal',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://api.txtlocal.com/send?apikey=textlocal-api-key' === $request->url()
            && 'textlocal-api-key' === $query['apikey']
            && $request->isForm()
            && '447123456789' === $request['numbers']
            && 'Laravel' === $request['sender']
            && 'Hello from Textlocal' === $request['message'];
    });

    expect($result)->toEqual($response);
});
