<?php

declare(strict_types=1);

arch('the ippanel driver depends on the core package, not the other way around')
    ->expect('Misaf\LaravelSmsGatewayIppanel')
    ->toUse('Misaf\LaravelSmsGateway\SmsGatewayDriver');
