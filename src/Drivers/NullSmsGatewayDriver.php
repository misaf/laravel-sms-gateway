<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

/**
 * Sends nothing to a remote provider and always reports success. This is the
 * default driver for local and testing environments.
 */
final class NullSmsGatewayDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return new Response(new PsrResponse(
            200,
            ['Content-Type' => 'application/json'],
            (string) json_encode(['sent' => true, 'data' => $data]),
        ));
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://null.sms-gateway.test/';
    }
}
