---
name: laravel-sms-gateway-messagebird-development
description: Guidance for developing the misaf/laravel-sms-gateway-messagebird package, the MessageBird driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-messagebird development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-messagebird` and split out to its own read-only repository on release.

## Layout

- `src/MessageBirdDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/MessageBirdServiceProvider.php` — registers the `messagebird` driver on the manager.
- `config/laravel-sms-gateway-messagebird.php` — provider credentials.
- `tests/Feature/MessageBirdDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-messagebird.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
