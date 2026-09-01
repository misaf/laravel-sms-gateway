<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTwilio;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver;

final class TwilioDriver extends SmsGatewayDriver
{
    public function __construct(
        string $baseUrl,
        private readonly string $accountSid,
        private readonly string $authToken,
        int $serverTimeout = 5,
        int $clientTimeout = 6,
        int $retryTimes = 2,
        int $retrySleepMilliseconds = 100,
    ) {
        parent::__construct($baseUrl, $serverTimeout, $clientTimeout, $retryTimes, $retrySleepMilliseconds);
    }

    protected function name(): string
    {
        return 'twilio';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function sendRequest(array $data): Response
    {
        // Twilio scopes every endpoint under the account, so the SID belongs to
        // the path rather than to the configurable base URL.
        return $this->request()->post($this->accountSid . '/Messages.json', $data);
    }

    protected function configure(PendingRequest $request): PendingRequest
    {
        return $request->withBasicAuth($this->accountSid, $this->authToken)->asForm();
    }
}
