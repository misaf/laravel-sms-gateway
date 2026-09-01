## Laravel SMS Gateway Sunway

This package adds the `sunway` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-sunway.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('sunway')`. Never
  instantiate `SunwayDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('sunway')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
