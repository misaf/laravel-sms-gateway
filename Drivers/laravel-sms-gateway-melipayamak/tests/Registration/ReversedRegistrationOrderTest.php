<?php

declare(strict_types=1);

use Misaf\LaravelSmsGateway\SmsGatewayManager;
use Misaf\LaravelSmsGatewayMelipayamak\MelipayamakDriver;

test('the driver resolves through the manager when its provider boots first', function (): void {
    expect(app(SmsGatewayManager::class)->driver('melipayamak'))->toBeInstanceOf(MelipayamakDriver::class);
});

test('the driver resolves through the facade accessor when its provider boots first', function (): void {
    expect(app('sms-gateway')->driver('melipayamak'))->toBeInstanceOf(MelipayamakDriver::class);
});
