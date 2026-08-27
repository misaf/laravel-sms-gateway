<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayIppanel;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class IppanelDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('services.jspd', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://ippanel.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->asForm()
            ->withQueryParameters([
                'uname' => $this->driverConfig('username'),
                'pass'  => $this->driverConfig('password'),
            ]);
    }
}
