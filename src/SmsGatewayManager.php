<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use LogicException;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Drivers\NullSmsGatewayDriver;

/**
 * Resolves SMS gateways. The core package ships only the "null" driver;
 * concrete provider drivers register themselves via {@see extend()} from
 * their own packages.
 *
 * @method \Illuminate\Http\Client\Response send(array<string, mixed> $data)
 */
final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): string
    {
        $driver = Config::get('laravel-sms-gateway.default');

        return is_string($driver) && '' !== $driver ? $driver : 'null';
    }

    public function gateway(?string $driver = null): SmsGateway
    {
        $gateway = $this->driver($driver);

        if ( ! $gateway instanceof SmsGateway) {
            throw new LogicException('The configured SMS gateway must implement the SmsGateway contract.');
        }

        return $gateway;
    }

    protected function createNullDriver(): SmsGateway
    {
        return new NullSmsGatewayDriver();
    }

    /**
     * @param string $driver
     */
    protected function createDriver($driver): mixed
    {
        $driverInstance = parent::createDriver($driver);

        if ($driverInstance instanceof SmsGatewayDriver) {
            $driverInstance->setDriverName($driver);
        }

        return $driverInstance;
    }
}
