<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMelipayamak;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class MelipayamakDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('SendSMS/SendSMS', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://rest.payamak-panel.com/api/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->asForm()
            ->withQueryParameters([
                'username' => $this->driverConfig('username'),
                'password' => $this->driverConfig('password'),
            ]);
    }
}
