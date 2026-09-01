## Laravel SMS Gateway Kavenegar

This package adds the `kavenegar` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-kavenegar.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('kavenegar')`. Never
  instantiate `KavenegarDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('kavenegar')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
