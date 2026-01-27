<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Override;
use Misaf\LaravelSmsGateway\SmsGatewayServiceProvider;

abstract class TestCase extends TestbenchTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SmsGatewayServiceProvider::class,
        ];
    }
}
