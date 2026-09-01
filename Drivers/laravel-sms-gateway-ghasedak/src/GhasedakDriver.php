<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayGhasedak;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class GhasedakDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $apiKey,
        int $serverTimeout,
        int $clientTimeout,
        int $retryTimes,
        int $retrySleepMilliseconds,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);

        self::requireConfigured($apiKey, 'Ghasedak API key');
    }

    protected function name(): string
    {
        return 'ghasedak';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('sms/send/simple', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withHeader('apikey', $this->apiKey);
    }
}
