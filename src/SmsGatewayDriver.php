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
    final public function send(array $data, ?string $endpoint = null): Response
    {
        $endpoint ??= $this->endpoint();

        return $this->request()->post('' === $endpoint ? $this->driverBaseUrl() : $endpoint, $data);
    }

    final public function request(): PendingRequest
    {
        $request = Http::baseUrl($this->driverBaseUrl())
            ->timeout(Config::integer('sms_gateway.defaults.timeout'))
            ->connectTimeout(Config::integer('sms_gateway.defaults.connect_timeout'))
            ->withHeaders($this->headers());

        return $this->configureRequest($request)->afterResponse(function (Response $response, Request $request): Response {
            SmsSent::dispatch($this->driverName(), $request, $response);

            return $response;
        });
    }

    final public function endpoint(string $name = 'default'): string
    {
        $servicePath = "services.{$this->driverName()}.endpoints.{$name}";
        $driverPath = "sms_gateway.drivers.{$this->driverName()}.endpoints.{$name}";
        $default = $this->defaultEndpoints()[$name] ?? ('default' === $name ? '' : $name);

        return Config::string($servicePath, fn(): string => Config::string($driverPath, $default));
    }

    abstract protected function driverName(): string;

    abstract protected function defaultBaseUrl(): string;

    /**
     * @return array<string, string>
     */
    protected function defaultEndpoints(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        $apiKeyHeader = $this->apiKeyHeader();
        $apiKey = $this->serviceConfigString('api_key');

        if (null === $apiKeyHeader || '' === $apiKey) {
            return [];
        }

        return [
            $apiKeyHeader => $apiKey,
        ];
    }

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

    protected function serviceConfigString(string $key, string $default = ''): string
    {
        return Config::string("services.{$this->driverName()}.{$key}", $default);
    }

    private function driverBaseUrl(): string
    {
        return $this->serviceConfigString('base_url', $this->defaultBaseUrl());
    }
}
