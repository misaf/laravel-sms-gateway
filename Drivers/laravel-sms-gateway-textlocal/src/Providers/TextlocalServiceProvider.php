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
        $this->callAfterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager): void {
            $manager->extend('textlocal', fn(): SmsGateway => new TextlocalDriver(
                apiKey: Config::string('sms-gateway-textlocal.api_key'),
                baseUrl: Config::string('sms-gateway-textlocal.base_url'),
                timeout: Config::integer('sms-gateway.defaults.timeout'),
                connectTimeout: Config::integer('sms-gateway.defaults.connect_timeout'),
            ));
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Textlocal', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-textlocal') ?? 'Unknown',
        ]);
    }
}
