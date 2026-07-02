<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use Misaf\LaravelSmsGateway\Drivers\GhasedakDriver;
use Misaf\LaravelSmsGateway\Drivers\IppanelDriver;
use Misaf\LaravelSmsGateway\Drivers\KavenegarDriver;
use Misaf\LaravelSmsGateway\Drivers\MagfaDriver;
use Misaf\LaravelSmsGateway\Drivers\MessageBirdDriver;
use Misaf\LaravelSmsGateway\Drivers\MelipayamakDriver;
use Misaf\LaravelSmsGateway\Drivers\PlivoDriver;
use Misaf\LaravelSmsGateway\Drivers\SmsIrDriver;
use Misaf\LaravelSmsGateway\Drivers\SunwayDriver;
use Misaf\LaravelSmsGateway\Drivers\TextlocalDriver;
use Misaf\LaravelSmsGateway\Drivers\TwilioDriver;
use Misaf\LaravelSmsGateway\Drivers\VonageDriver;

final class SmsGatewayManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return Config::string('sms_gateway.default', 'ghasedak');
    }

    protected function createGhasedakDriver(): GhasedakDriver
    {
        return $this->container->make(GhasedakDriver::class);
    }

    protected function createIppanelDriver(): IppanelDriver
    {
        return $this->container->make(IppanelDriver::class);
    }

    protected function createKavenegarDriver(): KavenegarDriver
    {
        return $this->container->make(KavenegarDriver::class);
    }

    protected function createMagfaDriver(): MagfaDriver
    {
        return $this->container->make(MagfaDriver::class);
    }

    protected function createMessagebirdDriver(): MessageBirdDriver
    {
        return $this->container->make(MessageBirdDriver::class);
    }

    protected function createMelipayamakDriver(): MelipayamakDriver
    {
        return $this->container->make(MelipayamakDriver::class);
    }

    protected function createPlivoDriver(): PlivoDriver
    {
        return $this->container->make(PlivoDriver::class);
    }

    protected function createSmsirDriver(): SmsIrDriver
    {
        return $this->container->make(SmsIrDriver::class);
    }

    protected function createSunwayDriver(): SunwayDriver
    {
        return $this->container->make(SunwayDriver::class);
    }

    protected function createTextlocalDriver(): TextlocalDriver
    {
        return $this->container->make(TextlocalDriver::class);
    }

    protected function createTwilioDriver(): TwilioDriver
    {
        return $this->container->make(TwilioDriver::class);
    }

    protected function createVonageDriver(): VonageDriver
    {
        return $this->container->make(VonageDriver::class);
    }
}
