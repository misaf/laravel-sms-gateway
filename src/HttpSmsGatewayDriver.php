<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Events\SmsSent;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;

abstract class HttpSmsGatewayDriver implements SmsGatewayHandlerInterface
{
    final public function send(): PendingRequest
    {
        $request = Http::baseUrl($this->gateway())
            ->timeout($this->timeout())
            ->connectTimeout($this->connectTimeout());

        $headers = $this->headers();

        if ([] !== $headers) {
            $request = $request->withHeaders($headers);
        }

        $queryParameters = $this->queryParameters();

        if ([] !== $queryParameters) {
            $request = $request->withQueryParameters($queryParameters);
        }

        if ($this->acceptsJson()) {
            $request = $request->acceptJson();
        }

        return $request->afterResponse(function (Response $response, Request $request): void {
            SmsSent::dispatch($this->driverName(), $request, $response);
        });
    }

    abstract protected function driverName(): string;

    abstract protected function defaultGateway(): string;

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function queryParameters(): array
    {
        return [];
    }

    protected function acceptsJson(): bool
    {
        return false;
    }

    private function gateway(): string
    {
        return Config::string($this->configPath('gateway'), $this->defaultGateway());
    }

    private function timeout(): int
    {
        return Config::integer($this->configPath('timeout'), 10);
    }

    private function connectTimeout(): int
    {
        return Config::integer($this->configPath('connect_timeout'), 5);
    }

    private function configPath(string $key): string
    {
        return "sms_gateway.drivers.{$this->driverName()}.{$key}";
    }
}
