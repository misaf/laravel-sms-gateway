<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayVonage;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class VonageDriver implements SmsGateway
{
    private const string DEFAULT_BASE_URL = 'https://rest.nexmo.com/';

    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $apiSecret = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('sms/json', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->acceptJson()
            ->asForm()
            ->withQueryParameters([
                'api_key'    => $this->apiKey,
                'api_secret' => $this->apiSecret,
            ])
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('vonage', $request, $response);

                return $response;
            });
    }
}
