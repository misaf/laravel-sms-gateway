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

abstract class SmsGatewayDriver implements SmsGatewayHandlerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    abstract public function send(array $data): Response;

    final public function request(): PendingRequest
    {
        $request = Http::baseUrl($this->driverBaseUrl())
            ->timeout(Config::integer('sms_gateway.defaults.timeout'))
            ->connectTimeout(Config::integer('sms_gateway.defaults.connect_timeout'))
            ->withHeaders($this->driverHeaders());

        return $this->configureRequest($request)->afterResponse(function (Response $response, Request $request): Response {
            SmsSent::dispatch($this->driverName(), $request, $response);

            return $response;
        });
    }

    abstract protected function driverName(): string;

    abstract protected function defaultBaseUrl(): string;

    /**
     * Apply driver-specific options to the outgoing request.
     */
    protected function configureRequest(PendingRequest $request): PendingRequest
    {
        return $request;
    }

    protected function apiKeyHeader(): ?string
    {
        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function driverHeaders(): array
    {
        $apiKeyHeader = $this->apiKeyHeader();
        $apiKey = $this->driverConfig('api_key');

        if (null === $apiKeyHeader || '' === $apiKey) {
            return [];
        }

        return [
            $apiKeyHeader => $apiKey,
        ];
    }

    protected function driverConfig(string $key, string $default = ''): string
    {
        return Config::string("services.{$this->driverName()}.{$key}", $default);
    }

    private function driverBaseUrl(): string
    {
        return $this->driverConfig('base_url', $this->defaultBaseUrl());
    }
}
