---
name: laravel-sms-gateway-sunway-development
description: Guidance for developing the misaf/laravel-sms-gateway-sunway package, the Sunway driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-sunway development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-sunway` and split out to its own read-only repository on release.

## Layout

- `src/SunwayDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/SunwayServiceProvider.php` — registers the `sunway` driver on the manager.
- `config/laravel-sms-gateway-sunway.php` — provider credentials.
- `tests/Feature/SunwayDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-sunway.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
