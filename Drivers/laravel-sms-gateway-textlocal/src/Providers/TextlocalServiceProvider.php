<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTextlocal\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayTextlocal\TextlocalDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class TextlocalServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-textlocal')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-textlocal');
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
                $manager->extend('textlocal', fn(): SmsGateway => new TextlocalDriver(
                    apiKey: Config::string('sms-gateway-textlocal.api_key'),
                    baseUrl: Config::string('sms-gateway-textlocal.base_url'),
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
        AboutCommand::add('Laravel SMS Gateway Textlocal', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-textlocal') ?? 'Unknown',
        ]);
    }
}
