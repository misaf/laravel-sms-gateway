<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayPlivo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class PlivoDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $authId,
        private readonly string $authToken,
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 2,
        int $retrySleepMilliseconds = 100,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function name(): string
    {
        return 'plivo';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        // Plivo scopes every endpoint under the account, so the auth ID belongs
        // to the path rather than to the configurable base URL.
        return $this->request()->post($this->authId . '/Message/', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withBasicAuth($this->authId, $this->authToken)->acceptJson();
    }
}
