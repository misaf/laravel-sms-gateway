<?php

declare(strict_types=1);

use Misaf\LaravelSmsGateway\Tests\TestCase;

pest()->extend(TestCase::class)->in(
    'Feature',
    '../src/Drivers/*/tests/Feature',
);
