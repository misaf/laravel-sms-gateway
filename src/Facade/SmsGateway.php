<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Facade;

use Illuminate\Support\Facades\Facade;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;
use Misaf\LaravelSmsGateway\SmsGatewayManager;

/**
 * @method static SmsGatewayDriver driver(?string $driver = null)
 * @method static SmsGatewayManager extend(string $driver, \Closure $callback)
 * @method static ?string getDefaultDriver()
 */
final class SmsGateway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sms-gateway';
    }
}
