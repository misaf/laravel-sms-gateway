<?php

declare(strict_types=1);

arch('the vonage driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayVonage')
    ->toUse('Misaf\LaravelSmsGateway\Contracts\SmsGateway');
