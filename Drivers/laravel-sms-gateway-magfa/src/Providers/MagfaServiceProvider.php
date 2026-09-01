<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayMagfa\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayMagfa\MagfaDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class MagfaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-magfa')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-magfa');
            });
    }

    public function packageRegistered(): void
    {
        // Deferred, so this provider never resolves the manager itself. Doing
        // so during registration would build a throwaway manager whenever this
        // package is registered before the core one, silently losing the
        // driver.
        $this->callAfterResolving(
            SmsGatewayManager::class,
            function (SmsGatewayManager $manager): void {
                $manager->extend('magfa', fn(): SmsGateway => new MagfaDriver(
                    baseUrl: Config::string('sms-gateway-magfa.base_url'),
                    username: Config::string('sms-gateway-magfa.username'),
                    password: Config::string('sms-gateway-magfa.password'),
                    serverTimeout: Config::integer('sms-gateway-magfa.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-magfa.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-magfa.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-magfa.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Magfa', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-magfa') ?? 'Unknown',
        ]);
    }
}
