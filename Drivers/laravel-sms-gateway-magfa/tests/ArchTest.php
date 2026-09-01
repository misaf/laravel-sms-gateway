<?php

declare(strict_types=1);

arch('the magfa driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayMagfa')
    ->toUse('Misaf\LaravelSmsGateway\Contracts\SmsGateway');
