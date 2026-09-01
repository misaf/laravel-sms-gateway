<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Drivers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSendFailed;
use Misaf\LaravelSmsGateway\Events\SmsSending;
use Misaf\LaravelSmsGateway\Events\SmsSendUnreachable;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Throwable;

/**
 * Shared HTTP behaviour for the gateway drivers: timeouts, the retry policy and
 * the lifecycle events. A driver supplies its name, its credentials and the
 * endpoint it sends to; the base URL always comes from its config file.
 */
abstract class SmsGatewayDriver implements SmsGateway
{
    public function __construct(
        protected readonly string $baseUrl,
        protected readonly int $serverTimeout,
        protected readonly int $clientTimeout,
        protected readonly int $retryTimes,
        protected readonly int $retrySleepMilliseconds,
    ) {
        self::requireConfigured($baseUrl, 'base URL');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        SmsSending::dispatch($this->driverName(), $data);

        try {
            return $this->sendRequest($data);
        } catch (Throwable $exception) {
            // Only a gateway that was never reached lands here; a rejected send
            // has a response and is reported on the response path instead.
            SmsSendUnreachable::dispatch($this->driverName(), $exception);

            throw $exception;
        }
    }

    public function request(): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl)
            ->connectTimeout($this->serverTimeout)
            ->timeout($this->clientTimeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleepMilliseconds,
                $this->shouldRetry(...),
                throw: false,
            );

        return $this->configure($request)
            ->afterResponse(function (Response $response, Request $request): Response {
                if ($response->successful()) {
                    SmsSent::dispatch($this->driverName(), $request, $response);

                    return $response;
                }

                SmsSendFailed::dispatch($this->driverName(), $request, $response);

                return $response;
            });
    }

    abstract protected function driverName(): string;

    /**
     * @param array<string, mixed> $data
     */
    abstract protected function sendRequest(array $data): Response;

    /**
     * Apply the provider's credentials and body format to the pending request.
     */
    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request;
    }

    /**
     * Guard a value the driver needs to build a working request.
     *
     * A config default only covers a key that is absent, so a key that is
     * present but empty is rejected here rather than at the gateway.
     */
    protected static function requireConfigured(string $value, string $name): string
    {
        if ('' === $value) {
            throw new InvalidArgumentException(
                "The {$name} is empty. Set it in the driver's config file, or in the matching environment variable."
            );
        }

        return $value;
    }

    protected function shouldRetry(Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        return $exception instanceof RequestException
            && $exception->response->serverError();
    }
}
