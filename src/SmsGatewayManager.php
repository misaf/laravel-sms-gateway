<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Drivers\NullSmsGatewayDriver;

/**
 * @method \Illuminate\Http\Client\Response send(array<string, mixed> $data)
 */
final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return Config::string('sms-gateway.default');
    }

    protected function createNullDriver(): SmsGateway
    {
        return new NullSmsGatewayDriver();
    }
}
