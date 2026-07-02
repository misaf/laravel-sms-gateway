<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class MelipayamakDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'melipayamak';
    }

    protected function defaultGateway(): string
    {
        return 'https://rest.payamak-panel.com/api/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->asForm()
            ->withQueryParameters([
                'username' => $this->serviceConfigString('username'),
                'password' => $this->serviceConfigString('password'),
            ]);
    }
}
