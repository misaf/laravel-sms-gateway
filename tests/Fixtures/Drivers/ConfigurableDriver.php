<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

final class ConfigurableDriver implements SmsGatewayHandlerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data, ?string $endpoint = null): Response
    {
        return $this->request()->post($endpoint ?? 'messages', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('https://custom.example.com')
            ->timeout(10)
            ->connectTimeout(5);
    }
}
