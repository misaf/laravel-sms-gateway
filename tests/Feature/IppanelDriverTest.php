<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Uri;
use Misaf\LaravelSmsGateway\Facade\SmsGateway;

test('ippanel driver sends credentials as query parameters', function (): void {
    config()->set('sms_gateway.default', 'ippanel');
    config()->set('services.ippanel.username', 'ippanel-username');
    config()->set('services.ippanel.password', 'ippanel-password');

    $response = ['status' => 'ok'];

    Http::fake([
        'https://ippanel.com/services.jspd*' => Http::response($response, 200),
    ]);

    $result = SmsGateway::driver()->send()
        ->post('', [
            'op'      => 'send',
            'from'    => '3000505',
            'to'      => '09123456789',
            'message' => 'Hello from IPPanel',
        ])
        ->json();

    Http::assertSent(function (Request $request): bool {
        $query = Uri::of($request->url())->query()->all();

        return 'https://ippanel.com/services.jspd' === rtrim((string) strtok($request->url(), '?'), '/')
            && 'ippanel-username' === $query['uname']
            && 'ippanel-password' === $query['pass']
            && $request->isForm()
            && 'send' === $request['op']
            && '3000505' === $request['from']
            && '09123456789' === $request['to']
            && 'Hello from IPPanel' === $request['message'];
    });

    expect($result)->toEqual($response);
});
