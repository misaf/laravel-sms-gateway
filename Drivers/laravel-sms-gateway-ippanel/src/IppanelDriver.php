<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayIppanel;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class IppanelDriver extends SmsGatewayDriver
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

        self::requireConfigured($username, 'Ippanel username');
        self::requireConfigured($password, 'Ippanel password');
    }

    protected function name(): string
    {
        return 'ippanel';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        return $this->request()->post('services.jspd', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withQueryParameters([
            'uname' => $this->username,
            'pass'  => $this->password,
        ])->asForm();
    }
}
