<?php

declare(strict_types=1);

arch('the ghasedak driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayGhasedak')
    ->toUse('Misaf\LaravelSmsGateway\Contracts\SmsGateway');
