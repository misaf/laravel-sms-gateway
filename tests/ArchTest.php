<?php

declare(strict_types=1);

use Misaf\LaravelSmsGateway\SmsGatewayManager;

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel();

arch('the core remains provider neutral')
    ->expect([
        'Misaf\LaravelSmsGateway\Contracts',
        'Misaf\LaravelSmsGateway\Drivers',
        'Misaf\LaravelSmsGateway\Events',
        'Misaf\LaravelSmsGateway\Facades',
        'Misaf\LaravelSmsGateway\Providers',
        SmsGatewayManager::class,
    ])
    ->not->toUse([
        'Misaf\LaravelSmsGatewayGhasedak',
        'Misaf\LaravelSmsGatewayIppanel',
        'Misaf\LaravelSmsGatewayKavenegar',
        'Misaf\LaravelSmsGatewayMagfa',
        'Misaf\LaravelSmsGatewayMelipayamak',
        'Misaf\LaravelSmsGatewayMessageBird',
        'Misaf\LaravelSmsGatewayPlivo',
        'Misaf\LaravelSmsGatewaySmsIr',
        'Misaf\LaravelSmsGatewaySunway',
        'Misaf\LaravelSmsGatewayTextlocal',
        'Misaf\LaravelSmsGatewayTwilio',
        'Misaf\LaravelSmsGatewayVonage',
    ]);
