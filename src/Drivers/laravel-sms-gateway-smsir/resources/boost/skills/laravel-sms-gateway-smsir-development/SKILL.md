---
name: laravel-sms-gateway-smsir-development
description: Guidance for developing the misaf/laravel-sms-gateway-smsir package, the SMS.ir driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-smsir development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-smsir` and split out to its own read-only repository on release.

## Layout

- `src/SmsIrDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/SmsIrServiceProvider.php` — registers the `smsir` driver on the manager.
- `config/laravel-sms-gateway-smsir.php` — provider credentials.
- `tests/Feature/SmsIrDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-smsir.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
