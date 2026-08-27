<?php

declare(strict_types=1);

return [
    'api_key'    => env('SMS_GATEWAY_VONAGE_API_KEY'),
    'api_secret' => env('SMS_GATEWAY_VONAGE_API_SECRET'),
    'base_url'   => env('SMS_GATEWAY_VONAGE_BASE_URL'),
];
