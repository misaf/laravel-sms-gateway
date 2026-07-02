<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class TextlocalDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'textlocal';
    }

    protected function defaultGateway(): string
    {
        return 'https://api.txtlocal.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->asForm()
            ->withQueryParameters([
                'apikey' => $this->serviceConfigString('api_key'),
            ]);
    }
}
