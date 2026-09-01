## Laravel SMS Gateway Melipayamak

This package adds the `melipayamak` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-melipayamak.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('melipayamak')`. Never
  instantiate `MelipayamakDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('melipayamak')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
