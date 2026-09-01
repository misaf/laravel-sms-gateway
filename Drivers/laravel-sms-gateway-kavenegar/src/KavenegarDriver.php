<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayKavenegar;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class KavenegarDriver implements SmsGateway
{
    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('sms/send.json', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : "https://api.kavenegar.com/v1/{$this->apiKey}/")
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->asForm()
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('kavenegar', $request, $response);

                return $response;
            });
    }
}
