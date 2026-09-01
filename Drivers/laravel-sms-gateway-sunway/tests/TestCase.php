<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySunway\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Providers\SmsGatewayServiceProvider;
use Misaf\LaravelSmsGatewaySunway\Providers\SunwayServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Override;

abstract class TestCase extends TestbenchTestCase
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
        $app['config']->set('sms-gateway-sunway.username', 'test-username');
        $app['config']->set('sms-gateway-sunway.password', 'test-password');
    }

    /**
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SmsGatewayServiceProvider::class,
            SunwayServiceProvider::class,
        ];
    }
}
