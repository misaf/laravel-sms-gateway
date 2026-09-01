<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\SmsGatewayManager;

test('the config is merged without the application publishing it', function (): void {
    expect(config('sms-gateway'))->toBeArray()->not->toBeEmpty();
});

test('the config publish tag resolves to a single path', function (): void {
    expect(ServiceProvider::pathsToPublish(null, 'sms-gateway-config'))->toHaveCount(1);
});

test('the manager is a singleton bound to its alias', function (): void {
    expect(app(SmsGatewayManager::class))
        ->toBe(app('sms-gateway'))
        ->toBeInstanceOf(SmsGatewayManager::class);
});

test('the install command is registered', function (): void {
    expect(Artisan::all())->toHaveKey('sms-gateway:install');
});
