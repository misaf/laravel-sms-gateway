---
name: laravel-sms-gateway-magfa-development
description: Guidance for developing the misaf/laravel-sms-gateway-magfa package, the Magfa driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-magfa development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-magfa` and split out to its own read-only repository on release.

## Layout

- `src/MagfaDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/MagfaServiceProvider.php` — registers the `magfa` driver on the manager.
- `config/laravel-sms-gateway-magfa.php` — provider credentials.
- `tests/Feature/MagfaDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-magfa.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
