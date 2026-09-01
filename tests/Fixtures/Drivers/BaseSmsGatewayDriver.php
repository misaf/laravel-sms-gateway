<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests\Fixtures\Drivers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class BaseSmsGatewayDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl = 'https://base.example.com/',
        private readonly string $apiKey = 'secret',
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 0,
        int $retrySleepMilliseconds = 0,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);

        self::requireConfigured($apiKey, 'API key');
    }

    protected function name(): string
    {
        return 'base';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('messages', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withHeader('X-Api-Key', $this->apiKey);
    }
}
