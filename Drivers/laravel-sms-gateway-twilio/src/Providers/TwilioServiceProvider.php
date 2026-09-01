<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGatewayTwilio\Providers;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Misaf\LaravelSmsGateway\Contracts\SmsGateway;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayTwilio\TwilioDriver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class TwilioServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway-twilio')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('misaf/laravel-sms-gateway-twilio');
            });
    }

    public function packageRegistered(): void
    {
        $this->callAfterResolving(SmsGatewayManager::class, function (SmsGatewayManager $manager): void {
            $manager->extend('twilio', fn(): SmsGateway => new TwilioDriver(
                accountSid: Config::string('sms-gateway-twilio.account_sid'),
                authToken: Config::string('sms-gateway-twilio.auth_token'),
                baseUrl: Config::string('sms-gateway-twilio.base_url'),
                timeout: Config::integer('sms-gateway.defaults.timeout'),
                connectTimeout: Config::integer('sms-gateway.defaults.connect_timeout'),
            ));
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Laravel SMS Gateway Twilio', fn(): array => [
            'Version' => InstalledVersions::getPrettyVersion('misaf/laravel-sms-gateway-twilio') ?? 'Unknown',
        ]);
    }
}
