<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayGhasedak\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Providers\SmsGatewayServiceProvider;
use Misaf\LaravelSmsGatewayGhasedak\Providers\GhasedakServiceProvider;
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
     * The credential keys have no config default, so every test that resolves
     * the driver needs them set.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('sms-gateway-ghasedak.api_key', 'test-api-key');
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
            GhasedakServiceProvider::class,
            SmsGatewayServiceProvider::class,
        ];
    }
}
