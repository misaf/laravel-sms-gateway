---
name: laravel-sms-gateway-ghasedak-development
description: Guidance for developing the misaf/laravel-sms-gateway-ghasedak package, the Ghasedak driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-ghasedak development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-ghasedak` and split out to its own read-only repository on release.

## Layout

- `src/GhasedakDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/GhasedakServiceProvider.php` — registers the `ghasedak` driver on the manager.
- `config/sms-gateway-ghasedak.php` — provider credentials.
- `tests/Feature/GhasedakDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them all from `sms-gateway-ghasedak.*`; the timeout and retry
  keys fall back to the core `SMS_GATEWAY_*` variables when the driver-specific
  ones are unset.
- The driver extends `Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver`, which
  owns the timeouts, the retry policy and the `SmsSending`/`SmsSent`/
  `SmsSendFailed` events. Implement `name()`, `sendRequest()`
  and, for credentials, `configure()`. The base URL comes from the config file,
  which is the only place it is defined.
- Retry only connection failures and gateway 5xx responses; a rejected
  credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
