<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Interfaces;

use Illuminate\Http\Client\PendingRequest;

interface SmsGatewayHandlerInterface
{
    public function send(): PendingRequest;
}
