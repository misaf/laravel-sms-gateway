<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMessageBird;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class MessageBirdDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $accessKey,
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 2,
        int $retrySleepMilliseconds = 100,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function name(): string
    {
        return 'messagebird';
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
        return $request->withHeader('Authorization', 'AccessKey ' . $this->accessKey)
            ->acceptJson()
            ->asForm();
    }
}
