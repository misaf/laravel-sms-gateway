<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;

final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return Config::string('sms_gateway.default');
    }
}
