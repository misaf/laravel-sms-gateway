<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            ->beforeSending(function (Request $request) use (&$sentRequest): void {
                $sentRequest = $request;
            });

        // Laravel 12 invokes after-response callbacks without the request argument,
        // so the request must be paired manually via the beforeSending capture.
        return $this->configureRequest($request)->afterResponse(function (Response $response) use (&$sentRequest): void {
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

        if (null === $apiKeyHeader) {
            return [];
        }

        return [
            $apiKeyHeader => $this->serviceConfigString('api_key'),
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

    /**
     * Resolves `services.{driver}.{key}`, falling back to the camelCase
     * `sms_gateway.drivers.{driver}.{key}` entry, then the given default.
     */
    protected function serviceConfigString(string $key, string $default = ''): string
    {
        $servicePath = "services.{$this->driverName()}.{$key}";
        $driverPath = "sms_gateway.drivers.{$this->driverName()}." . Str::camel($key);

        return Config::string($servicePath, fn(): string => Config::string($driverPath, $default));
    }

    private function gateway(): string
    {
        return $this->serviceConfigString('gateway', $this->defaultGateway());
    }
}
