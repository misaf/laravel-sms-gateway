<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Contracts;

use Illuminate\Http\Client\Response;

interface SmsGateway
{
    /**
     * Send a message through the gateway.
     *
     * @param array<string, mixed> $data
     */
    public function send(array $data): Response;
}
