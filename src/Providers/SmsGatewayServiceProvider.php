<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
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
            ->hasConfigFile('laravel-sms-gateway')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('misaf/laravel-sms-gateway');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SmsGatewayManager::class);
        $this->app->alias(SmsGatewayManager::class, 'sms-gateway');
        $this->app->bind(
            SmsGateway::class,
            fn(): SmsGateway => $this->app->make(SmsGatewayManager::class)->gateway(),
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway'),
        ]);
    }
}
