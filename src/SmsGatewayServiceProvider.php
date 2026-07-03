<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
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
        $this->app->singleton(SmsGatewayManager::class);
        $this->app->alias(SmsGatewayManager::class, 'sms-gateway');
    }

    public function packageBooted(): void
    {
        if ( ! $this->app->runningInConsole()) {
            return;
        }

        AboutCommand::add('Laravel SMS Gateway', fn(): array => [
            'Version'        => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway') ?? 'Unknown',
            'Default Driver' => $this->app->make(SmsGatewayManager::class)->getDefaultDriver() ?: 'Not configured',
        ]);
    }
}
