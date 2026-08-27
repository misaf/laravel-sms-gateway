<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMessageBird;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class MessageBirdDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://rest.messagebird.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->withHeader('Authorization', 'AccessKey ' . $this->driverConfig('access_key'))
            ->acceptJson()
            ->asForm();
    }
}
