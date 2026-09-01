<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Throwable;

final class SmsSendFailed
{
    use Dispatchable;

    /**
     * The request and response are null when the gateway was never reached — a
     * connection error or timeout — in which case the exception is present.
     */
    public function __construct(
        public readonly string $driverName,
        public readonly ?Request $request = null,
        public readonly ?Response $response = null,
        public readonly ?Throwable $exception = null,
    ) {}
}
