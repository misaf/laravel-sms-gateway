<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class GhasedakDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'ghasedak';
    }

    protected function defaultGateway(): string
    {
        return 'https://api.ghasedak.me/v2/';
    }

    protected function apiKeyHeader(): string
    {
        return 'apikey';
    }
}
