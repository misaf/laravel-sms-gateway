---
name: laravel-sms-gateway-melipayamak-development
description: Guidance for developing the misaf/laravel-sms-gateway-melipayamak package, the Melipayamak driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-melipayamak development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-melipayamak` and split out to its own read-only repository on release.

## Layout

- `src/MelipayamakDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/MelipayamakServiceProvider.php` — registers the `melipayamak` driver on the manager.
- `config/laravel-sms-gateway-melipayamak.php` — provider credentials.
- `tests/Feature/MelipayamakDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-melipayamak.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
