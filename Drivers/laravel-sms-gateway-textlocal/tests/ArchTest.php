<?php

declare(strict_types=1);

arch('the textlocal driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayTextlocal')
    ->toUse('Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver');
