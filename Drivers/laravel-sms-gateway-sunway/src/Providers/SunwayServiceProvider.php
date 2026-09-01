<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySunway\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewaySunway\SunwayDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SunwayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-sunway')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-sunway');
            });
    }

    public function packageRegistered(): void
    {
        $this->callAfterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager): void {
            $manager->extend('sunway', fn(): SmsGateway => new SunwayDriver(
                username: Config::string('sms-gateway-sunway.username'),
                password: Config::string('sms-gateway-sunway.password'),
                baseUrl: Config::string('sms-gateway-sunway.base_url'),
                timeout: Config::integer('sms-gateway.defaults.timeout'),
                connectTimeout: Config::integer('sms-gateway.defaults.connect_timeout'),
            ));
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Sunway', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-sunway') ?? 'Unknown',
        ]);
    }
}
