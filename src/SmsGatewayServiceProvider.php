<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SmsGatewayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway')
            ->hasConfigFile('sms_gateway');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('sms-gateway', fn(Application $app): SmsGatewayManager => new SmsGatewayManager($app));
        $this->app->alias('sms-gateway', SmsGatewayManager::class);
    }
}
