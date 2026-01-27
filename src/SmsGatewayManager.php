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
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('sms_gateway.default');
    }

    /**
     * Create an instance of the Ghasedak driver.
     *
     * @return \Drivers\Ghasedak
     */
    protected function createGhasedakDriver()
    {
        return new GhasedakDriver();
    }

    /**
     * Create an instance of the Sunway driver.
     *
     * @return \Drivers\Ghasedak
     */
    protected function createSunwayDriver()
    {
        return new SunwayDriver();
    }
}
