<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMagfa;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class MagfaDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $username,
        private readonly string $password,
        int $serverTimeout,
        int $clientTimeout,
        int $retryTimes,
        int $retrySleepMilliseconds,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);

        self::requireConfigured($username, 'Magfa username');
        self::requireConfigured($password, 'Magfa password');
    }

    protected function name(): string
    {
        return 'magfa';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('send', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withBasicAuth($this->username, $this->password)->acceptJson();
    }
}
