<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class SmsSending
{
    use Dispatchable;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly string $driverName,
        public readonly array $data,
    ) {}
}
