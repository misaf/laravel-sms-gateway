<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMessageBird\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Providers\SmsGatewayServiceProvider;
use Misaf\LaravelSmsGatewayMessageBird\Providers\MessageBirdServiceProvider;
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
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SmsGatewayServiceProvider::class,
            MessageBirdServiceProvider::class,
        ];
    }
}
