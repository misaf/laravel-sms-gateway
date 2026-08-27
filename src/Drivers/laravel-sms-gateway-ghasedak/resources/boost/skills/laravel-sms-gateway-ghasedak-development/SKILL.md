---
name: laravel-sms-gateway-ghasedak-development
description: Guidance for developing the misaf/laravel-sms-gateway-ghasedak package, the Ghasedak driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-ghasedak development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-ghasedak` and split out to its own read-only repository on release.

## Layout

- `src/GhasedakDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/GhasedakServiceProvider.php` — registers the `ghasedak` driver on the manager.
- `config/laravel-sms-gateway-ghasedak.php` — provider credentials.
- `tests/Feature/GhasedakDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-ghasedak.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
