---
name: laravel-sms-gateway-magfa-development
description: Guidance for developing the misaf/laravel-sms-gateway-magfa package, the Magfa driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-magfa development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-magfa` and split out to its own read-only repository on release.

## Layout

- `src/MagfaDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/MagfaServiceProvider.php` — registers the `magfa` driver on the manager.
- `config/sms-gateway-magfa.php` — provider credentials.
- `tests/Feature/MagfaDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them from `sms-gateway-magfa.*` and
  `sms-gateway.defaults.*`.
- Build requests with the driver's own `request()`, which applies the timeouts,
  the retry policy, and dispatches the `SmsSent` event via `afterResponse()`.
- Retry only connection failures and gateway 5xx responses, via `shouldRetry()`;
  a rejected credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
