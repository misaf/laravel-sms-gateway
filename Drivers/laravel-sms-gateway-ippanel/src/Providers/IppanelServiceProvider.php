<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayIppanel\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
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
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-ippanel');
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
                $manager->extend('ippanel', fn(): SmsGateway => new IppanelDriver(
                    baseUrl: Config::string('sms-gateway-ippanel.base_url'),
                    username: Config::string('sms-gateway-ippanel.username'),
                    password: Config::string('sms-gateway-ippanel.password'),
                    serverTimeout: Config::integer('sms-gateway-ippanel.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-ippanel.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-ippanel.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-ippanel.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway IPPanel', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-ippanel') ?? 'Unknown',
        ]);
    }
}
