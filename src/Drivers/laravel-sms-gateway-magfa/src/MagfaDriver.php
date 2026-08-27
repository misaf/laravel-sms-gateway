<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMagfa;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class MagfaDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('send', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://sms.magfa.com/api/http/sms/v2/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withBasicAuth($this->driverConfig('username'), $this->driverConfig('password'))
            ->acceptJson();
    }
}
