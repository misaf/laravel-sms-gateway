<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayPlivo\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayPlivo\PlivoDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class PlivoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-plivo')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-plivo');
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
                $manager->extend('plivo', fn(): SmsGateway => new PlivoDriver(
                    baseUrl: Config::string('sms-gateway-plivo.base_url'),
                    authId: Config::string('sms-gateway-plivo.auth_id'),
                    authToken: Config::string('sms-gateway-plivo.auth_token'),
                    serverTimeout: Config::integer('sms-gateway-plivo.timeout.server'),
                    clientTimeout: Config::integer('sms-gateway-plivo.timeout.client'),
                    retryTimes: Config::integer('sms-gateway-plivo.retry.times'),
                    retrySleepMilliseconds: Config::integer('sms-gateway-plivo.retry.sleep_milliseconds'),
                ));
            }
        );
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Plivo', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-plivo') ?? 'Unknown',
        ]);
    }
}
