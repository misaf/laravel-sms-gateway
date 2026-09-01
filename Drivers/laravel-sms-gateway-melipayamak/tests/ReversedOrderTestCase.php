<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMelipayamak\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Providers\SmsGatewayServiceProvider;
use Misaf\LaravelSmsGatewayMelipayamak\Providers\MelipayamakServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Override;

abstract class ReversedOrderTestCase extends TestbenchTestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    /**
     * Registers this driver package before the core package, the order Laravel's
     * package discovery is free to pick. Nothing may depend on the core
     * provider having run first.
     *
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            MelipayamakServiceProvider::class,
            SmsGatewayServiceProvider::class,
        ];
    }
}
