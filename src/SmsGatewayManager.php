<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Manager;

final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): ?string
    {
        $driver = $this->config->get('sms_gateway.default');

        return is_string($driver) ? $driver : null;
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
