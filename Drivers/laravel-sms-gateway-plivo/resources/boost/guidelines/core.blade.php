## Laravel SMS Gateway Plivo

This package adds the `plivo` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-plivo.php`, not in `config/services.php`.
- The base URL and every credential in `config/sms-gateway-plivo.php` are
  required and may not be empty; the driver throws an `InvalidArgumentException`
  at resolution rather than sending a request it cannot authenticate.
- Resolve the driver through the manager: `SmsGateway::driver('plivo')`. Never
  instantiate `PlivoDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('plivo')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response, `SmsSendFailed` on a failed one, or
  `SmsSendUnreachable` when the gateway was never reached.
