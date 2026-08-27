<?php

declare(strict_types=1);

arch('the kavenegar driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayKavenegar')
    ->toUse('Misaf\LaravelSmsGateway\SmsGatewayDriver');
