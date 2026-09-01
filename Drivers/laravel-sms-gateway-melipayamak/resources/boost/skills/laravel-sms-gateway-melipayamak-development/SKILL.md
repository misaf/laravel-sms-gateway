---
name: laravel-sms-gateway-melipayamak-development
description: Guidance for developing the misaf/laravel-sms-gateway-melipayamak package, the Melipayamak driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-melipayamak development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-melipayamak` and split out to its own read-only repository on release.

## Layout

- `src/MelipayamakDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/MelipayamakServiceProvider.php` — registers the `melipayamak` driver on the manager.
- `config/sms-gateway-melipayamak.php` — provider credentials.
- `tests/Feature/MelipayamakDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them from `sms-gateway-melipayamak.*` and
  `sms-gateway.defaults.*`.
- Build requests with the driver's own `request()`, which applies the timeouts,
  the retry policy, and dispatches the `SmsSent` event via `afterResponse()`.
- Retry only connection failures and gateway 5xx responses, via `shouldRetry()`;
  a rejected credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
