## Laravel SMS Gateway Textlocal

This package adds the `textlocal` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-textlocal.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('textlocal')`. Never
  instantiate `TextlocalDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('textlocal')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
