<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers;

use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class CustomSmsGatewayDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    protected function driverName(): string
    {
        return 'custom';
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://custom.example.com';
    }
}
