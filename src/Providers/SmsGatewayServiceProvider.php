<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Providers;

use Composer\InstalledVersions;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Console\AboutCommand;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SmsGatewayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            SmsGatewayManager::class,
            fn(Application $app): SmsGatewayManager => new SmsGatewayManager($app),
        );

        $this->app->alias(SmsGatewayManager::class, 'sms-gateway');
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway'),
        ]);
    }
}
