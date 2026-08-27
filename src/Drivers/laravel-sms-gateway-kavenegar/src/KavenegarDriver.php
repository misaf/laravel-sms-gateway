<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayKavenegar;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class KavenegarDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('sms/send.json', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return "https://api.kavenegar.com/v1/{$this->driverConfig('api_key')}/";
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request->asForm();
    }
}
