<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Misaf\LaravelSmsGateway\HttpSmsGatewayDriver;

final class SunwayDriver extends HttpSmsGatewayDriver
{
    protected function driverName(): string
    {
        return 'sunway';
    }

    protected function defaultGateway(): string
    {
        return 'https://sms.sunwaysms.com/smsws/HttpService.ashx';
    }

    /**
     * @return array<string, string>
     */
    protected function queryParameters(): array
    {
        return [
            'UserName' => $this->serviceConfigString('username', 'username'),
            'Password' => $this->serviceConfigString('password', 'password'),
        ];
    }
}
