## Laravel SMS Gateway Twilio

This package adds the `twilio` driver to `misaf/laravel-sms-gateway`.

- Credentials live in `config/sms-gateway-twilio.php`, not in `config/services.php`.
- Resolve the driver through the manager: `SmsGateway::driver('twilio')`. Never
  instantiate `TwilioDriver` directly — it needs its driver name injected.
- Send with `SmsGateway::driver('twilio')->send([...])`; the payload is passed
  through to the provider unchanged.
- Every send dispatches `Misaf\LaravelSmsGateway\Events\SmsSending`, then
  `SmsSent` on a successful response or `SmsSendFailed` on a failed one.
