<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class CustomSmsGatewayDriver implements SmsGateway
{
    private const string DEFAULT_BASE_URL = 'https://custom.example.com';

    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
        private readonly string $driverName = 'custom',
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    public function request(): PendingRequest
    {
        $request = Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);

        if ('' !== $this->apiKey) {
            $request = $request->withHeader('apikey', $this->apiKey);
        }

        return $request->afterResponse(function (Response $response, Request $request): Response {
            SmsSent::dispatch($this->driverName, $request, $response);

            return $response;
        });
    }
}
