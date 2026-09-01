---
name: laravel-sms-gateway-textlocal-development
description: Guidance for developing the misaf/laravel-sms-gateway-textlocal package, the Textlocal driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-textlocal development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-textlocal` and split out to its own read-only repository on release.

## Layout

- `src/TextlocalDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/TextlocalServiceProvider.php` — registers the `textlocal` driver on the manager.
- `config/sms-gateway-textlocal.php` — provider credentials.
- `tests/Feature/TextlocalDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them from `sms-gateway-textlocal.*` and
  `sms-gateway.defaults.*`.
- Build requests with the driver's own `request()`, which applies the timeouts,
  the retry policy, and dispatches the `SmsSent` event via `afterResponse()`.
- Retry only connection failures and gateway 5xx responses, via `shouldRetry()`;
  a rejected credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
