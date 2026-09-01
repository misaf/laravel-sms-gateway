<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTwilio;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

final class TwilioDriver implements SmsGateway
{
    public function __construct(
        private readonly string $accountSid = '',
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
        return $this->request()->post('Messages.json', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/")
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withBasicAuth($this->accountSid, $this->authToken)
            ->asForm()
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('twilio', $request, $response);

                return $response;
            });
    }
}
