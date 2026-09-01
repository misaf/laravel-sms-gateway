## Laravel SMS Gateway SMS.ir

This package adds the `smsir` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-smsir.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('smsir')`. Never
  instantiate `SmsIrDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('smsir')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
