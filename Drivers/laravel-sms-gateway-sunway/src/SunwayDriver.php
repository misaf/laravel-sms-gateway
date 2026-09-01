<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySunway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class SunwayDriver implements SmsGateway
{
    private const string DEFAULT_BASE_URL = 'https://sms.sunwaysms.com/smsws/';

    public function __construct(
        private readonly string $username = '',
        private readonly string $password = '',
        private readonly string $baseUrl = '',
        private readonly int $timeout = 10,
        private readonly int $connectTimeout = 5,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->get('HttpService.ashx', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withQueryParameters([
                'UserName' => $this->username,
                'Password' => $this->password,
            ])
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('sunway', $request, $response);

                return $response;
            });
    }
}
