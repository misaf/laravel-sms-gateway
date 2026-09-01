<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewaySmsIr\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewaySmsIr\SmsIrDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SmsIrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-smsir')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-smsir');
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
                $manager->extend('smsir', fn(): SmsGateway => new SmsIrDriver(
                    baseUrl: Config::string('sms-gateway-smsir.base_url'),
                    apiKey: Config::string('sms-gateway-smsir.api_key'),
                    serverTimeout: Config::integer('sms-gateway-smsir.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-smsir.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-smsir.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-smsir.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway SMS.ir', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-smsir') ?? 'Unknown',
        ]);
    }
}
