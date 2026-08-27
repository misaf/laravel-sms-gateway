<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use LogicException;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\Events\SmsSent;

abstract class SmsGatewayDriver implements SmsGateway
{
    private ?string $driverName = null;

    /**
     * @param array<string, mixed> $data
     */
    abstract public function send(array $data): Response;

    final public function request(): PendingRequest
    {
        $request = Http::baseUrl($this->driverBaseUrl())
            ->timeout(Config::integer('laravel-sms-gateway.defaults.timeout'))
            ->connectTimeout(Config::integer('laravel-sms-gateway.defaults.connect_timeout'));

        $apiKeyHeader = $this->apiKeyHeader();
        $apiKey = $this->driverConfig('api_key');

        if (null !== $apiKeyHeader && '' !== $apiKey) {
            $request = $request->withHeader($apiKeyHeader, $apiKey);
        }

        return $this->configureRequest($request)->afterResponse(function (Response $response, Request $request): Response {
            SmsSent::dispatch($this->driverName(), $request, $response);

            return $response;
        });
    }

    final public function setDriverName(string $driverName): void
    {
        $this->driverName = $driverName;
    }

    abstract protected function defaultBaseUrl(): string;

    /**
     * The driver name, set from the registration key by the manager unless overridden.
     */
    protected function driverName(): string
    {
        if (null === $this->driverName) {
            throw new LogicException(sprintf('Driver [%s] has no name. Resolve it through the SMS gateway manager or override driverName().', static::class));
        }

        return $this->driverName;
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
     * The config namespace this driver reads its credentials from. Driver
     * packages ship a config file named after the package itself.
     */
    protected function configKey(): string
    {
        return 'laravel-sms-gateway-' . $this->driverName();
    }

    /**
     * Read a credential from this driver's config file. Unset keys resolve to
     * null in the published config file, so treat null and empty alike.
     */
    protected function driverConfig(string $key, string $default = ''): string
    {
        $value = Config::get("{$this->configKey()}.{$key}");

        return is_string($value) && '' !== $value ? $value : $default;
    }

    private function driverBaseUrl(): string
    {
        return $this->driverConfig('base_url', $this->defaultBaseUrl());
    }
}
