<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayKavenegar;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class KavenegarDriver extends SmsGatewayDriver
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

        self::requireConfigured($apiKey, 'Kavenegar API key');
    }

    protected function name(): string
    {
        return 'kavenegar';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        // Kavenegar scopes every endpoint under the API key, so it belongs to the path.
        return $this->request()->post($this->apiKey . '/sms/send.json', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->asForm();
    }
}
