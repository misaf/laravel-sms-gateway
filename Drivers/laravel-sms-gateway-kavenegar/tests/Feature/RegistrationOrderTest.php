<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayKavenegar\KavenegarDriver;

test('the driver resolves through the manager', function (): void {
    expect(app(SmsGatewayManager::class)->driver('kavenegar'))->toBeInstanceOf(KavenegarDriver::class);
});

test('the driver config is merged without the application publishing it', function (): void {
    expect(config('sms-gateway-kavenegar'))->toBeArray()->not->toBeEmpty();
});

test('the config publish tag resolves to a single path', function (): void {
    expect(ServiceProvider::pathsToPublish(null, 'sms-gateway-kavenegar-config'))->toHaveCount(1);
});

test('the install command is registered', function (): void {
    expect(Artisan::all())->toHaveKey('sms-gateway-kavenegar:install');
});

test('the timeout and retry config has its own defaults', function (): void {
    expect(config('sms-gateway-kavenegar.timeout.server'))->toBe(5)
        ->and(config('sms-gateway-kavenegar.timeout.client'))->toBe(6)
        ->and(config('sms-gateway-kavenegar.retry.times'))->toBe(2)
        ->and(config('sms-gateway-kavenegar.retry.sleep_milliseconds'))->toBe(100);
});

test('casts string timeout and retry values from the environment to integers', function (): void {
    $_SERVER['SMS_GATEWAY_KAVENEGAR_SERVER_TIMEOUT'] = '15';
    $_SERVER['SMS_GATEWAY_KAVENEGAR_CLIENT_TIMEOUT'] = '30';
    $_SERVER['SMS_GATEWAY_KAVENEGAR_RETRY_TIMES'] = '4';
    $_SERVER['SMS_GATEWAY_KAVENEGAR_RETRY_SLEEP_MILLISECONDS'] = '250';

    try {
        $config = require __DIR__ . '/../../config/sms-gateway-kavenegar.php';
    } finally {
        unset(
            $_SERVER['SMS_GATEWAY_KAVENEGAR_SERVER_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_KAVENEGAR_CLIENT_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_KAVENEGAR_RETRY_TIMES'],
            $_SERVER['SMS_GATEWAY_KAVENEGAR_RETRY_SLEEP_MILLISECONDS'],
        );
    }

    expect($config['timeout']['server'])->toBe(15)
        ->and($config['timeout']['client'])->toBe(30)
        ->and($config['retry']['times'])->toBe(4)
        ->and($config['retry']['sleep_milliseconds'])->toBe(250);
});
