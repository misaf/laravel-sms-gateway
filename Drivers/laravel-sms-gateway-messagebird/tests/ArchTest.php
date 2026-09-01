<?php

declare(strict_types=1);

arch('the messagebird driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayMessageBird')
    ->toUse('Misaf\LaravelSmsGateway\Contracts\SmsGateway');
