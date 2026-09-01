---
name: laravel-sms-gateway-ippanel-development
description: Guidance for developing the misaf/laravel-sms-gateway-ippanel package, the IPPanel driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-ippanel development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-ippanel` and split out to its own read-only repository on release.

## Layout

- `src/IppanelDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/IppanelServiceProvider.php` — registers the `ippanel` driver on the manager.
- `config/sms-gateway-ippanel.php` — provider credentials.
- `tests/Feature/IppanelDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them from `sms-gateway-ippanel.*` and
  `sms-gateway.defaults.*`.
- Build requests with the driver's own `request()`, which applies the timeouts
  and dispatches the `SmsSent` event via `afterResponse()`.
- Keep the driver free of any dependency on sibling driver packages.
