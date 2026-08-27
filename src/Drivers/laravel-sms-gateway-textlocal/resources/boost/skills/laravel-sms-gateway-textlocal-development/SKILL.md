---
name: laravel-sms-gateway-textlocal-development
description: Guidance for developing the misaf/laravel-sms-gateway-textlocal package, the Textlocal driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-textlocal development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-textlocal` and split out to its own read-only repository on release.

## Layout

- `src/TextlocalDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/TextlocalServiceProvider.php` — registers the `textlocal` driver on the manager.
- `config/laravel-sms-gateway-textlocal.php` — provider credentials.
- `tests/Feature/TextlocalDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-textlocal.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
