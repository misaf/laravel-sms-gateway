<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayKavenegar\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayKavenegar\KavenegarDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class KavenegarServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-kavenegar')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-kavenegar');
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
                $manager->extend('kavenegar', fn(): SmsGateway => new KavenegarDriver(
                    baseUrl: Config::string('sms-gateway-kavenegar.base_url'),
                    apiKey: Config::string('sms-gateway-kavenegar.api_key'),
                    serverTimeout: Config::integer('sms-gateway-kavenegar.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-kavenegar.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-kavenegar.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-kavenegar.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Kavenegar', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-kavenegar') ?? 'Unknown',
        ]);
    }
}
