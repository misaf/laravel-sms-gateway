## Laravel SMS Gateway Magfa

This package adds the `magfa` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-magfa.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('magfa')`. Never
  instantiate `MagfaDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('magfa')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
