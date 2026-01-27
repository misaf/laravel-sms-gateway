<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class GhasedakDriver implements SmsGatewayHandlerInterface
{
    public function send(): PendingRequest
    {
        return Http::withUrlParameters([
            'UserName' => config('sms-gateway.drivers.ghasedak.username'),
            'Password' => config('sms-gateway.drivers.ghasedak.password'),
        ])->baseUrl('https://sms.sunwaysms.com/smsws/')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
