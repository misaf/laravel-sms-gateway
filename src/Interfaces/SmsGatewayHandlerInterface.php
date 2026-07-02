<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Interfaces;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

interface SmsGatewayHandlerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function send(array $data, ?string $endpoint = null): Response;

    public function request(): PendingRequest;
}
