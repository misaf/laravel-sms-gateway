<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers\Sunway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class SunwayDriver implements SmsGatewayHandlerInterface
{
    public function send(): PendingRequest
    {
        return Http::withUrlParameters([
            'UserName' => config('sms-gateway.drivers.sunway.username'),
            'Password' => config('sms-gateway.drivers.sunway.password'),
        ])->baseUrl('https://sms.sunwaysms.com/smsws/')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
