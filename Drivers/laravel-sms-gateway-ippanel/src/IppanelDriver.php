<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayIppanel;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class IppanelDriver implements SmsGateway
{
    private const string DEFAULT_BASE_URL = 'https://ippanel.com/';

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
        return $this->request()->post('services.jspd', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->asForm()
            ->withQueryParameters([
                'uname' => $this->username,
                'pass'  => $this->password,
            ])
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('ippanel', $request, $response);

                return $response;
            });
    }
}
