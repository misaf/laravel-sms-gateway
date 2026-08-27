<?php

declare(strict_types=1);

return [
    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID'),
    'auth_token'  => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN'),
    'base_url'    => env('SMS_GATEWAY_TWILIO_BASE_URL'),
];
