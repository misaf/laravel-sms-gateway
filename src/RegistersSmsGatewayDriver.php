<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Contracts\Foundation\Application;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;
use Spatie\LaravelPackageTools\Package;

trait RegistersSmsGatewayDriver
{
    abstract protected function packageName(): string;

    abstract protected function driverName(): string;

    /**
     * @return class-string<SmsGatewayHandlerInterface>
     */
    abstract protected function driverClass(): string;

    public function configurePackage(Package $package): void
    {
        $package->name($this->packageName());
    }

    public function packageBooted(): void
    {
        $driverName = $this->driverName();
        $driverClass = $this->driverClass();

        $this->app->afterResolving('sms-gateway', function (SmsGatewayManager $manager) use ($driverClass, $driverName): void {
            /** @var class-string<SmsGatewayHandlerInterface> $driverClass */
            $manager->extend(
                $driverName,
                fn(Application $app): SmsGatewayHandlerInterface => $app->make($driverClass),
            );
        });
    }
}
