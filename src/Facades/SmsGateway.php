<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Facades;

use Illuminate\Support\Facades\Facade;
use Misaf\LaravelSmsGateway\SmsGatewayManager;

/**
 * @method static \Misaf\LaravelSmsGateway\Contracts\SmsGateway driver(string|null $driver = null)
 * @method static SmsGatewayManager extend(string $driver, \Closure $callback)
 * @method static string getDefaultDriver()
 * @method static \Illuminate\Http\Client\Response send(array<string, mixed> $data)
 *
 * @see SmsGatewayManager
 */
final class SmsGateway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sms-gateway';
    }
}
