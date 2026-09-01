<?php

declare(strict_types=1);

arch('the sunway driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewaySunway')
    ->toUse('Misaf\LaravelSmsGateway\Contracts\SmsGateway');
