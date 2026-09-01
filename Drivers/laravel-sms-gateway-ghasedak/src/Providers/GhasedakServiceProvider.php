<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayGhasedak\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayGhasedak\GhasedakDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class GhasedakServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-ghasedak')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-ghasedak');
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
                $manager->extend('ghasedak', fn(): SmsGateway => new GhasedakDriver(
                    baseUrl: Config::string('sms-gateway-ghasedak.base_url'),
                    apiKey: Config::string('sms-gateway-ghasedak.api_key'),
                    serverTimeout: Config::integer('sms-gateway-ghasedak.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-ghasedak.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-ghasedak.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-ghasedak.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Ghasedak', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-ghasedak') ?? 'Unknown',
        ]);
    }
}
