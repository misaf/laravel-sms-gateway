<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayPlivo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class PlivoDriver implements SmsGateway
{
    public function __construct(
        private readonly string $authId = '',
        private readonly string $authToken = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('Message/', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : "https://api.plivo.com/v1/Account/{$this->authId}/")
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withBasicAuth($this->authId, $this->authToken)
            ->acceptJson()
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('plivo', $request, $response);

                return $response;
            });
    }
}
