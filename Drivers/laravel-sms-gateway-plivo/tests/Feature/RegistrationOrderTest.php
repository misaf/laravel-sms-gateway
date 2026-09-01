<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayPlivo\PlivoDriver;

test('the driver resolves through the manager', function (): void {
    expect(app(SmsGatewayManager::class)->driver('plivo'))->toBeInstanceOf(PlivoDriver::class);
});

test('the driver config is merged without the application publishing it', function (): void {
    expect(config('sms-gateway-plivo'))->toBeArray()->not->toBeEmpty();
});

test('the config publish tag resolves to a single path', function (): void {
    expect(ServiceProvider::pathsToPublish(null, 'sms-gateway-plivo-config'))->toHaveCount(1);
});

test('the install command is registered', function (): void {
    expect(Artisan::all())->toHaveKey('sms-gateway-plivo:install');
});

test('the timeout and retry config has its own defaults', function (): void {
    expect(config('sms-gateway-plivo.timeout.server'))->toBe(5)
        ->and(config('sms-gateway-plivo.timeout.client'))->toBe(6)
        ->and(config('sms-gateway-plivo.retry.times'))->toBe(2)
        ->and(config('sms-gateway-plivo.retry.sleep_milliseconds'))->toBe(100);
});

test('casts string timeout and retry values from the environment to integers', function (): void {
    $_SERVER['SMS_GATEWAY_PLIVO_SERVER_TIMEOUT'] = '15';
    $_SERVER['SMS_GATEWAY_PLIVO_CLIENT_TIMEOUT'] = '30';
    $_SERVER['SMS_GATEWAY_PLIVO_RETRY_TIMES'] = '4';
    $_SERVER['SMS_GATEWAY_PLIVO_RETRY_SLEEP_MILLISECONDS'] = '250';

    try {
        $config = require __DIR__ . '/../../config/sms-gateway-plivo.php';
    } finally {
        unset(
            $_SERVER['SMS_GATEWAY_PLIVO_SERVER_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_PLIVO_CLIENT_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_PLIVO_RETRY_TIMES'],
            $_SERVER['SMS_GATEWAY_PLIVO_RETRY_SLEEP_MILLISECONDS'],
        );
    }

    expect($config['timeout']['server'])->toBe(15)
        ->and($config['timeout']['client'])->toBe(30)
        ->and($config['retry']['times'])->toBe(4)
        ->and($config['retry']['sleep_milliseconds'])->toBe(250);
});
