<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;

final class SmsSent
{
    use Dispatchable;

    public function __construct(
        public readonly string $driverName,
        public readonly Request $request,
        public readonly Response $response,
    ) {}
}
