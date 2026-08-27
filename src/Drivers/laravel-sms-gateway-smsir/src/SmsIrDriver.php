<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySmsIr;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class SmsIrDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('send/bulk', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://api.sms.ir/v1/';
    }

    protected function apiKeyHeader(): string
    {
        return 'X-API-KEY';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request->acceptJson();
    }
}
