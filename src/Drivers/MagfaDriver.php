<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class MagfaDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'magfa';
    }

    protected function defaultGateway(): string
    {
        return 'https://sms.magfa.com/api/http/sms/v2/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withBasicAuth($this->serviceConfigString('username'), $this->serviceConfigString('password'))
            ->acceptJson()
            ->asJson();
    }
}
