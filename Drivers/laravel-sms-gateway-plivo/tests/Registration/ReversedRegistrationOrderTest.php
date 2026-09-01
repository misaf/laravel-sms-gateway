<?php

declare(strict_types=1);

use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayPlivo\PlivoDriver;

test('the driver resolves through the manager when its provider boots first', function (): void {
    expect(app(SmsGatewayManager::class)->driver('plivo'))->toBeInstanceOf(PlivoDriver::class);
});

test('the driver resolves through the facade accessor when its provider boots first', function (): void {
    expect(app('sms-gateway')->driver('plivo'))->toBeInstanceOf(PlivoDriver::class);
});
