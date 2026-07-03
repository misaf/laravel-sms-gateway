<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Facade;

use Illuminate\Support\Facades\Facade;
use Misaf\LaravelSmsGateway\SmsGatewayManager;

/**
 * @method static \Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface driver(?string $driver = null)
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
