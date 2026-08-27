<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayIppanel\Providers;

use Composer\InstalledVersions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayIppanel\IppanelDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class IppanelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-ippanel')
            ->hasConfigFile('laravel-sms-gateway-ippanel')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-ippanel');
            });
    }

    public function packageRegistered(): void
    {
        $this->callAfterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager): void {
            $manager->extend('ippanel', fn(Application $app): SmsGateway => $app->make(IppanelDriver::class));
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway IPPanel', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-ippanel') ?? 'Unknown',
        ]);
    }
}
