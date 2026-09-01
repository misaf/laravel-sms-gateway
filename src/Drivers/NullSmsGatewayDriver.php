<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSending;
use Misaf\LaravelSmsGateway\Events\SmsSent;

/**
 * Reports every message as sent without reaching a gateway.
 *
 * It dispatches the same lifecycle events as a real driver so listeners can be
 * exercised locally and in tests; only the failure events are out of reach,
 * since this driver never fails.
 */
final class NullSmsGatewayDriver implements SmsGateway
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        SmsSending::dispatch($this->driverName(), $data);

        $request = new Request(new PsrRequest(
            'POST',
            'null://sms',
            ['Content-Type' => 'application/json'],
            $this->encode($data),
        ));

        $response = new Response(new PsrResponse(200, ['Content-Type' => 'application/json'], $this->encode([
            'sent' => true,
            'data' => $data,
        ])));

        SmsSent::dispatch($this->driverName(), $request, $response);

        return $response;
    }

    private function driverName(): string
    {
        return 'null';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function encode(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
