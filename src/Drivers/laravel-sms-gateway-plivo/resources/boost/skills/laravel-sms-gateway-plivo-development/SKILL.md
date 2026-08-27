---
name: laravel-sms-gateway-plivo-development
description: Guidance for developing the misaf/laravel-sms-gateway-plivo package, the Plivo driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-plivo development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-plivo` and split out to its own read-only repository on release.

## Layout

- `src/PlivoDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/PlivoServiceProvider.php` — registers the `plivo` driver on the manager.
- `config/laravel-sms-gateway-plivo.php` — provider credentials.
- `tests/Feature/PlivoDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-plivo.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
