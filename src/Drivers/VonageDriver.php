<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class VonageDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'vonage';
    }

    protected function defaultGateway(): string
    {
        return 'https://rest.nexmo.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->acceptJson()
            ->withQueryParameters([
                'api_key'    => $this->serviceConfigString('api_key'),
                'api_secret' => $this->serviceConfigString('api_secret'),
            ]);
    }
}
