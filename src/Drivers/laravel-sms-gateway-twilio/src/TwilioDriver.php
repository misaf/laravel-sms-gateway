<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTwilio;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class TwilioDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('Messages.json', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return "https://api.twilio.com/2010-04-01/Accounts/{$this->driverConfig('account_sid')}/";
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withBasicAuth($this->driverConfig('account_sid'), $this->driverConfig('auth_token'))
            ->asForm();
    }
}
