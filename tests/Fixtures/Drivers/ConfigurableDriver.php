<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class ConfigurableDriver implements SmsGatewayHandlerInterface
{
    public function send(): PendingRequest
    {
        return Http::baseUrl('https://custom.example.com')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
