<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;

final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): ?string
    {
        $driver = Config::get('sms_gateway.default');

        return is_string($driver) ? $driver : null;
    }
}
