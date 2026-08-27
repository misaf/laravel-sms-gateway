<?php

declare(strict_types=1);

return [
    'auth_id'    => env('SMS_GATEWAY_PLIVO_AUTH_ID'),
    'auth_token' => env('SMS_GATEWAY_PLIVO_AUTH_TOKEN'),
    'base_url'   => env('SMS_GATEWAY_PLIVO_BASE_URL'),
];
