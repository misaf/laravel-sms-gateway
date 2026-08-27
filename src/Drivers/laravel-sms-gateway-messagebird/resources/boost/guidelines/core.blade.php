## Laravel SMS Gateway MessageBird

This package adds the `messagebird` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/laravel-sms-gateway-messagebird.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('messagebird')`. Never
  instantiate `MessageBirdDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('messagebird')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every response dispatches `Misaf\LaravelSmsGateway\Events\SmsSent`.
