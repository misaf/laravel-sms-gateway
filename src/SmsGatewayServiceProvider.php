<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Composer\InstalledVersions;
use Illuminate\Contracts\Foundation\Application;
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
        $this->app->singleton('sms-gateway', fn(Application $app): SmsGatewayManager => new SmsGatewayManager($app));
        $this->app->alias('sms-gateway', SmsGatewayManager::class);
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway', fn(): array => [
            'Version'        => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway') ?? 'Unknown',
            'Default Driver' => config('sms_gateway.default') ?: 'Not configured',
        ]);
    }
}
