<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class MessageBirdDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'messagebird';
    }

    protected function defaultGateway(): string
    {
        return 'https://rest.messagebird.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withHeader('Authorization', 'AccessKey ' . $this->serviceConfigString('access_key'))
            ->acceptJson()
            ->asJson();
    }
}
