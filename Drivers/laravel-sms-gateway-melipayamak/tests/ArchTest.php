<?php

declare(strict_types=1);

arch('the melipayamak driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayMelipayamak')
    ->toUse('Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver');
