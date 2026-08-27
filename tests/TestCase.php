<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Misaf\LaravelSmsGateway\Providers\SmsGatewayServiceProvider;
use Misaf\LaravelSmsGatewayGhasedak\Providers\GhasedakServiceProvider;
use Misaf\LaravelSmsGatewayIppanel\Providers\IppanelServiceProvider;
use Misaf\LaravelSmsGatewayKavenegar\Providers\KavenegarServiceProvider;
use Misaf\LaravelSmsGatewayMagfa\Providers\MagfaServiceProvider;
use Misaf\LaravelSmsGatewayMelipayamak\Providers\MelipayamakServiceProvider;
use Misaf\LaravelSmsGatewayMessageBird\Providers\MessageBirdServiceProvider;
use Misaf\LaravelSmsGatewayPlivo\Providers\PlivoServiceProvider;
use Misaf\LaravelSmsGatewaySmsIr\Providers\SmsIrServiceProvider;
use Misaf\LaravelSmsGatewaySunway\Providers\SunwayServiceProvider;
use Misaf\LaravelSmsGatewayTextlocal\Providers\TextlocalServiceProvider;
use Misaf\LaravelSmsGatewayTwilio\Providers\TwilioServiceProvider;
use Misaf\LaravelSmsGatewayVonage\Providers\VonageServiceProvider;
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
            GhasedakServiceProvider::class,
            IppanelServiceProvider::class,
            KavenegarServiceProvider::class,
            MagfaServiceProvider::class,
            MelipayamakServiceProvider::class,
            MessageBirdServiceProvider::class,
            PlivoServiceProvider::class,
            SmsIrServiceProvider::class,
            SunwayServiceProvider::class,
            TextlocalServiceProvider::class,
            TwilioServiceProvider::class,
            VonageServiceProvider::class,
        ];
    }
}
