<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayVonage;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class VonageDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('sms/json', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://rest.nexmo.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->acceptJson()
            ->asForm()
            ->withQueryParameters([
                'api_key'    => $this->driverConfig('api_key'),
                'api_secret' => $this->driverConfig('api_secret'),
            ]);
    }
}
