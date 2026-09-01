<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Twilio API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Twilio SMS API (https://twilio.com). The account sid
    | scopes the default base URL to your account and both values are sent as
    | HTTP Basic authentication on every request.
    |
    */

    'account_sid' => env('SMS_GATEWAY_TWILIO_ACCOUNT_SID', ''),
    'auth_token'  => env('SMS_GATEWAY_TWILIO_AUTH_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The endpoint the Twilio driver sends requests to. Defaults to the
    | account-scoped "https://api.twilio.com/2010-04-01/Accounts/{account_sid}/".
    | Override when a proxy or a sandbox environment requires a different host.
    |
    */

    'base_url' => env('SMS_GATEWAY_TWILIO_BASE_URL', ''),

];
