## Laravel SMS Gateway Plivo

This package adds the `plivo` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-plivo.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('plivo')`. Never
  instantiate `PlivoDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('plivo')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
