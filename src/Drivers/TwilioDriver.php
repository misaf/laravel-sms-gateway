<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class TwilioDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'twilio';
    }

    protected function defaultGateway(): string
    {
        return "https://api.twilio.com/2010-04-01/Accounts/{$this->serviceConfigString('account_sid')}/";
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withBasicAuth($this->serviceConfigString('account_sid'), $this->serviceConfigString('auth_token'))
            ->asForm();
    }
}
