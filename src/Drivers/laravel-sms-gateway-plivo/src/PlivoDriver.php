<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayPlivo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class PlivoDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('Message/', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return "https://api.plivo.com/v1/Account/{$this->driverConfig('auth_id')}/";
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withBasicAuth($this->driverConfig('auth_id'), $this->driverConfig('auth_token'))
            ->acceptJson();
    }
}
