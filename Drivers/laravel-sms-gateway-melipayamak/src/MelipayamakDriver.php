<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMelipayamak;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class MelipayamakDriver extends SmsGatewayDriver
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

        self::requireConfigured($username, 'Melipayamak username');
        self::requireConfigured($password, 'Melipayamak password');
    }

    protected function name(): string
    {
        return 'melipayamak';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('SendSMS/SendSMS', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withQueryParameters([
            'username' => $this->username,
            'password' => $this->password,
        ])->asForm();
    }
}
