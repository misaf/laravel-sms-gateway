---
name: laravel-sms-gateway-ippanel-development
description: Guidance for developing the misaf/laravel-sms-gateway-ippanel package, the IPPanel driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-ippanel development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-ippanel` and split out to its own read-only repository on release.

## Layout

- `src/IppanelDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/IppanelServiceProvider.php` — registers the `ippanel` driver on the manager.
- `config/laravel-sms-gateway-ippanel.php` — provider credentials.
- `tests/Feature/IppanelDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-ippanel.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
