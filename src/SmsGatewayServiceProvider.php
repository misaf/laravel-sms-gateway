<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Contracts\Foundation\Application;
use Misaf\LaravelSmsGateway\Drivers\GhasedakDriver;
use Misaf\LaravelSmsGateway\Drivers\KavenegarDriver;
use Misaf\LaravelSmsGateway\Drivers\SmsIrDriver;
use Misaf\LaravelSmsGateway\Drivers\SunwayDriver;
use Misaf\LaravelSmsGateway\Interfaces\SmsGatewayHandlerInterface;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SmsGatewayServiceProvider extends PackageServiceProvider
{
    /**
     * @var array<string, class-string<SmsGatewayHandlerInterface>>
     */
    private const array BuiltInDrivers = [
        'ghasedak' => GhasedakDriver::class,
        'kavenegar' => KavenegarDriver::class,
        'smsir' => SmsIrDriver::class,
        'sunway' => SunwayDriver::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sms-gateway')
            ->hasConfigFile('sms_gateway');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('sms-gateway', fn (Application $app): SmsGatewayManager => new SmsGatewayManager($app));
    }

    public function packageBooted(): void
    {
        $this->app->afterResolving('sms-gateway', function (SmsGatewayManager $manager): void {
            foreach (self::BuiltInDrivers as $driverName => $driverClass) {
                $manager->extend(
                    $driverName,
                    fn (Application $app): SmsGatewayHandlerInterface => $app->make($driverClass),
                );
            }
        });
    }
}
