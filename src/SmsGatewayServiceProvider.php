<?php

declare(strict_types=1);

namespace Misaf\LaravelSmsGateway;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

final class SmsGatewayServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerSmsGatewayManager();

        $this->publishSmsGatewayConfig();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['sms-gateway'];
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerSmsGatewayManager();

        $this->registerSmsGatewayConfig();
    }

    /**
     * publish the SMS Gateway config.
     *
     * @return void
     */
    private function publishSmsGatewayConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sms_gateway.php' => config_path('sms_gateway.php'),
        ]);
    }

    /**
     * Register the SMS Gateway config.
     *
     * @return void
     */
    private function registerSmsGatewayConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms_gateway.php',
            'sms-gateway',
        );
    }

    /**
     * Register the SMS Gateway manager instance.
     *
     * @return void
     */
    private function registerSmsGatewayManager(): void
    {
        $this->app->singleton('sms-gateway', fn(Application $app) => new SmsGatewayManager($app));
    }
}
