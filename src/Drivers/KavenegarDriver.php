<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class KavenegarDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'kavenegar';
    }

    protected function defaultGateway(): string
    {
        return 'https://api.kavenegar.com/v1/';
    }

    protected function apiKeyHeader(): string
    {
        return 'apikey';
    }
}
