<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Facade;

use Illuminate\Support\Facades\Facade;

final class SmsGateway extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sms-gateway';
    }
}
