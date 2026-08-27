<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySunway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class SunwayDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->get('HttpService.ashx', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://sms.sunwaysms.com/smsws/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request->withQueryParameters([
            'UserName' => $this->driverConfig('username'),
            'Password' => $this->driverConfig('password'),
        ]);
    }
}
