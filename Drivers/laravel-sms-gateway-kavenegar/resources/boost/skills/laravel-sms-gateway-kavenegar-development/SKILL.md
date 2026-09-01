---
name: laravel-sms-gateway-kavenegar-development
description: Guidance for developing the misaf/laravel-sms-gateway-kavenegar package, the Kavenegar driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-kavenegar development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-kavenegar` and split out to its own read-only repository on release.

## Layout

- `src/KavenegarDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/KavenegarServiceProvider.php` — registers the `kavenegar` driver on the manager.
- `config/sms-gateway-kavenegar.php` — provider credentials.
- `tests/Feature/KavenegarDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them from `sms-gateway-kavenegar.*` and
  `sms-gateway.defaults.*`.
- Build requests with the driver's own `request()`, which applies the timeouts,
  the retry policy, and dispatches the `SmsSent` event via `afterResponse()`.
- Retry only connection failures and gateway 5xx responses, via `shouldRetry()`;
  a rejected credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
