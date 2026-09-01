<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayVonage;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class VonageDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $apiKey,
        private readonly string $apiSecret,
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 2,
        int $retrySleepMilliseconds = 100,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function name(): string
    {
        return 'vonage';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('sms/json', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withQueryParameters([
            'api_key'    => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ])->acceptJson()->asForm();
    }
}
