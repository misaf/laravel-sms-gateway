<?php

declare(strict_types=1);

arch('the twilio driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayTwilio')
    ->toUse('Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver');
