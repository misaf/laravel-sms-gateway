<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;

/**
 * The gateway answered and rejected the send. A gateway that was never reached
 * raises SmsSendUnreachable instead, which carries the exception.
 */
final class SmsSendFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $driverName,
        public readonly Request $request,
        public readonly Response $response,
    ) {}
}
