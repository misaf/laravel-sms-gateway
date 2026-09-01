## Laravel SMS Gateway Ghasedak

This package adds the `ghasedak` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-ghasedak.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('ghasedak')`. Never
  instantiate `GhasedakDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('ghasedak')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
