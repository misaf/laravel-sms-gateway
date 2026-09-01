<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayVonage\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayVonage\VonageDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class VonageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-vonage')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-vonage');
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
                $manager->extend('vonage', fn(): SmsGateway => new VonageDriver(
                    apiKey: Config::string('sms-gateway-vonage.api_key'),
                    apiSecret: Config::string('sms-gateway-vonage.api_secret'),
                    baseUrl: Config::string('sms-gateway-vonage.base_url'),
                    serverTimeout: Config::integer('sms-gateway.defaults.server_timeout'),
                    clientTimeout: Config::integer('sms-gateway.defaults.client_timeout'),
                    retryTimes: Config::integer('sms-gateway.defaults.retry_times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway.defaults.retry_sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Vonage', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-vonage') ?? 'Unknown',
        ]);
    }
}
