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
        $this->registerSmsGatewayConfig();
        $this->registerSmsGatewayManager();
    }

    /**
     * Publish the SMS Gateway config.
     */
    private function publishSmsGatewayConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sms_gateway.php' => config_path('sms_gateway.php'),
        ]);
    }

    /**
     * Register the SMS Gateway config.
     */
    private function registerSmsGatewayConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms_gateway.php',
            'sms_gateway',
        );
    }

    /**
     * Register the SMS Gateway manager instance.
     */
    private function registerSmsGatewayManager(): void
    {
        $this->app->singleton('sms-gateway', fn(Application $app) => new SmsGatewayManager($app));
    }
}
