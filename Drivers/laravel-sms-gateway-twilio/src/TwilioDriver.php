<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTwilio;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Throwable;

final class TwilioDriver implements SmsGateway
{
    private const string DEFAULT_BASE_URL = 'https://api.twilio.com/2010-04-01/Accounts/';

    public function __construct(
        private readonly string $accountSid = '',
        private readonly string $authToken = '',
        private readonly string $baseUrl = '',
        private readonly int $serverTimeout = 5,
        private readonly int $clientTimeout = 6,
        private readonly int $retryTimes = 2,
        private readonly int $retrySleepMilliseconds = 100,
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
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL . $this->accountSid . '/')
            ->connectTimeout($this->serverTimeout)
            ->timeout($this->clientTimeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleepMilliseconds,
                $this->shouldRetry(...),
                throw: false,
            )
            ->withBasicAuth($this->accountSid, $this->authToken)
            ->asForm()
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('twilio', $request, $response);

                return $response;
            });
    }

    private function shouldRetry(Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        return $exception instanceof RequestException
            && $exception->response->serverError();
    }
}
