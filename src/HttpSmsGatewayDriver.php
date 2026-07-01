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
        $sentRequest = null;

        $request = Http::baseUrl($this->gateway())
            ->timeout(Config::integer('sms_gateway.defaults.timeout'))
            ->connectTimeout(Config::integer('sms_gateway.defaults.connect_timeout'))
            ->withHeaders($this->headers())
            ->withQueryParameters($this->queryParameters())
            ->beforeSending(function (Request $request) use (&$sentRequest): void {
                $sentRequest = $request;
            });

        if ($this->acceptsJson()) {
            $request = $request->acceptJson();
        }

        return $request->afterResponse(function (Response $response) use (&$sentRequest): void {
            if ($sentRequest instanceof Request) {
                SmsSent::dispatch($this->driverName(), $sentRequest, $response);
            }
        });
    }

    abstract protected function driverName(): string;

    abstract protected function defaultGateway(): string;

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        $apiKeyHeader = $this->apiKeyHeader();

        if ($apiKeyHeader === null) {
            return [];
        }

        return [
            $apiKeyHeader => $this->serviceConfigString('api_key', 'apiKey'),
        ];
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

    protected function apiKeyHeader(): ?string
    {
        return null;
    }

    protected function serviceConfigString(string $serviceKey, string $legacyDriverKey, string $default = ''): string
    {
        return Config::string(
            $this->serviceConfigPath($serviceKey),
            Config::string($this->configPath($legacyDriverKey), $default),
        );
    }

    private function gateway(): string
    {
        return Config::string($this->configPath('gateway'), $this->defaultGateway());
    }

    private function configPath(string $key): string
    {
        return "sms_gateway.drivers.{$this->driverName()}.{$key}";
    }

    private function serviceConfigPath(string $key): string
    {
        return "services.{$this->driverName()}.{$key}";
    }
}
