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
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Throwable;

/**
 * Shared HTTP behaviour for the gateway drivers: timeouts, the retry policy and
 * the lifecycle events. A driver supplies its name, the credentials it puts on
 * the request, and the endpoint it sends to. The base URL comes from the
 * driver's config file, which is the only place it is defined.
 */
abstract class SmsGatewayDriver implements SmsGateway
{
    public function __construct(
        protected readonly string $baseUrl,
        protected readonly int $serverTimeout = 5,
        protected readonly int $clientTimeout = 6,
        protected readonly int $retryTimes = 2,
        protected readonly int $retrySleepMilliseconds = 100,
    ) {
        if ('' === $baseUrl) {
            throw new InvalidArgumentException(
                'The base URL is empty. Set it in the driver\'s config file, or in the matching environment variable.'
            );
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        SmsSending::dispatch($this->name(), $data);

        try {
            return $this->sendRequest($data);
        } catch (Throwable $exception) {
            SmsSendFailed::dispatch($this->name(), exception: $exception);

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
                    SmsSent::dispatch($this->name(), $request, $response);

                    return $response;
                }

                SmsSendFailed::dispatch($this->name(), $request, $response);

                return $response;
            });
    }

    /**
     * The name the driver is registered under, as used in the config and events.
     */
    abstract protected function name(): string;


    /**
     * Perform the provider-specific send call.
     *
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

    protected function shouldRetry(Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        return $exception instanceof RequestException
            && $exception->response->serverError();
    }
}
