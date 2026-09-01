<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayKavenegar;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Throwable;

final class KavenegarDriver implements SmsGateway
{
    public function __construct(
        private readonly string $apiKey = '',
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
        return $this->request()->post('sms/send.json', $data);
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('' !== $this->baseUrl ? $this->baseUrl : "https://api.kavenegar.com/v1/{$this->apiKey}/")
            ->connectTimeout($this->serverTimeout)
            ->timeout($this->clientTimeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleepMilliseconds,
                $this->shouldRetry(...),
                throw: false,
            )
            ->asForm()
            ->afterResponse(function (Response $response, Request $request): Response {
                SmsSent::dispatch('kavenegar', $request, $response);

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
