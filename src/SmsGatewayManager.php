<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return Config::string('sms_gateway.default', 'ghasedak');
    }

    protected function createDriver($driver): SmsGatewayHandlerInterface
    {
        $resolvedDriver = parent::createDriver($driver);

        if ( ! $resolvedDriver instanceof SmsGatewayHandlerInterface) {
            throw new InvalidArgumentException("Driver [{$driver}] must implement [" . SmsGatewayHandlerInterface::class . '].');
        }

        return $resolvedDriver;
    }
}
