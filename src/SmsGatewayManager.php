<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Manager;
use Misaf\LaravelSmsGateway\Drivers\GhasedakDriver;
use Misaf\LaravelSmsGateway\Drivers\Sunway\SunwayDriver;

final class SmsGatewayManager extends Manager
{
    /**
     * Get the default SMS Gateway driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('sms_gateway.default');
    }

    /**
     * Create an instance of the Ghasedak driver.
     */
    protected function createGhasedakDriver(): GhasedakDriver
    {
        return new GhasedakDriver();
    }

    /**
     * Create an instance of the Sunway driver.
     */
    protected function createSunwayDriver(): SunwayDriver
    {
        return new SunwayDriver();
    }
}
