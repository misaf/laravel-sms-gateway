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
        return Http::withHeaders([
            'apikey' => config('sms_gateway.drivers.ghasedak.apiKey'),
        ])->baseUrl('https://api.ghasedak.me/v2/')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
