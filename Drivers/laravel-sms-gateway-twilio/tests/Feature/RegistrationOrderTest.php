<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayTwilio\TwilioDriver;

test('the driver resolves through the manager', function (): void {
    expect(app(SmsGatewayManager::class)->driver('twilio'))->toBeInstanceOf(TwilioDriver::class);
});

test('the driver config is merged without the application publishing it', function (): void {
    expect(config('sms-gateway-twilio'))->toBeArray()->not->toBeEmpty();
});

test('the config publish tag resolves to a single path', function (): void {
    expect(ServiceProvider::pathsToPublish(null, 'sms-gateway-twilio-config'))->toHaveCount(1);
});

test('the install command is registered', function (): void {
    expect(Artisan::all())->toHaveKey('sms-gateway-twilio:install');
});

test('the timeout and retry config has its own defaults', function (): void {
    expect(config('sms-gateway-twilio.timeout.server'))->toBe(5)
        ->and(config('sms-gateway-twilio.timeout.client'))->toBe(6)
        ->and(config('sms-gateway-twilio.retry.times'))->toBe(2)
        ->and(config('sms-gateway-twilio.retry.sleep_milliseconds'))->toBe(100);
});

test('casts string timeout and retry values from the environment to integers', function (): void {
    $_SERVER['SMS_GATEWAY_TWILIO_SERVER_TIMEOUT'] = '15';
    $_SERVER['SMS_GATEWAY_TWILIO_CLIENT_TIMEOUT'] = '30';
    $_SERVER['SMS_GATEWAY_TWILIO_RETRY_TIMES'] = '4';
    $_SERVER['SMS_GATEWAY_TWILIO_RETRY_SLEEP_MILLISECONDS'] = '250';

    try {
        $config = require __DIR__ . '/../../config/sms-gateway-twilio.php';
    } finally {
        unset(
            $_SERVER['SMS_GATEWAY_TWILIO_SERVER_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_TWILIO_CLIENT_TIMEOUT'],
            $_SERVER['SMS_GATEWAY_TWILIO_RETRY_TIMES'],
            $_SERVER['SMS_GATEWAY_TWILIO_RETRY_SLEEP_MILLISECONDS'],
        );
    }

    expect($config['timeout']['server'])->toBe(15)
        ->and($config['timeout']['client'])->toBe(30)
        ->and($config['retry']['times'])->toBe(4)
        ->and($config['retry']['sleep_milliseconds'])->toBe(250);
});
