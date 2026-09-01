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
        int $serverTimeout,
        int $clientTimeout,
        int $retryTimes,
        int $retrySleepMilliseconds,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);

        self::requireConfigured($accessKey, 'MessageBird access key');
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
