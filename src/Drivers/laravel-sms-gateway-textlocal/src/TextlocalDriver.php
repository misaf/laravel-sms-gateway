<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTextlocal;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class TextlocalDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('send/', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://api.txtlocal.com/';
    }

    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request
            ->asForm()
            ->withQueryParameters([
                'apikey' => $this->driverConfig('api_key'),
            ]);
    }
}
