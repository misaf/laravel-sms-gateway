## Laravel SMS Gateway IPPanel

This package adds the `ippanel` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-ippanel.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('ippanel')`. Never
  instantiate `IppanelDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('ippanel')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
