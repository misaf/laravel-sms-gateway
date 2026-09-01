<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;

final class NullSmsGatewayDriver implements SmsGateway
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return new Response(new PsrResponse(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'sent' => true,
            'data' => $data,
        ])));
    }
}
