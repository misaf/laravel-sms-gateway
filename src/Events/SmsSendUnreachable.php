<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Throwable;

/**
 * The gateway was never reached — a connection error or a timeout — so there is
 * no response to report. A gateway that answered and rejected the send raises
 * SmsSendFailed instead.
 */
final class SmsSendUnreachable
{
    use Dispatchable;

    public function __construct(
        public readonly string $driverName,
        public readonly Throwable $exception,
    ) {}
}
