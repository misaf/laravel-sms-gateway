<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayGhasedak;

use Illuminate\Http\Client\Response;
use Misaf\LaravelSmsGateway\SmsGatewayDriver;

final class GhasedakDriver extends SmsGatewayDriver
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response
    {
        return $this->request()->post('sms/send/simple', $data);
    }

    protected function defaultBaseUrl(): string
    {
        return 'https://api.ghasedak.me/v2/';
    }

    protected function apiKeyHeader(): string
    {
        return 'apikey';
    }
}
