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
        int $serverTimeout,
        int $clientTimeout,
        int $retryTimes,
        int $retrySleepMilliseconds,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);

        self::requireConfigured($authId, 'Plivo auth ID');
        self::requireConfigured($authToken, 'Plivo auth token');
    }

    protected function driverName(): string
    {
        return 'plivo';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        // Plivo scopes every endpoint under the account, so the auth ID belongs to the path.
        return $this->request()->post($this->authId . '/Message/', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withBasicAuth($this->authId, $this->authToken)->acceptJson();
    }
}
