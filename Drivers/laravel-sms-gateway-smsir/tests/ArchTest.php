<?php

declare(strict_types=1);

arch('the smsir driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewaySmsIr')
    ->toUse('Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver');
