<?php

declare(strict_types=1);

arch('the plivo driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayPlivo')
    ->toUse('Misaf\LaravelSmsGateway\SmsGatewayDriver');
