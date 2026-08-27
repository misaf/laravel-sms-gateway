## Laravel SMS Gateway Vonage

This package adds the `vonage` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/laravel-sms-gateway-vonage.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('vonage')`. Never
  instantiate `VonageDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('vonage')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
