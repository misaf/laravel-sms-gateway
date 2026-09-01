<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySunway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class SunwayDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $username,
        private readonly string $password,
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 2,
        int $retrySleepMilliseconds = 100,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function name(): string
    {
        return 'sunway';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->get('HttpService.ashx', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withQueryParameters([
            'UserName' => $this->username,
            'Password' => $this->password,
        ]);
    }
}
