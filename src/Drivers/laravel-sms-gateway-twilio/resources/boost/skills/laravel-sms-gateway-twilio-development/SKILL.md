---
name: laravel-sms-gateway-twilio-development
description: Guidance for developing the misaf/laravel-sms-gateway-twilio package, the Twilio driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-twilio development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`src/Drivers/laravel-sms-gateway-twilio` and split out to its own read-only repository on release.

## Layout

- `src/TwilioDriver.php` — extends `Misaf\LaravelSmsGateway\SmsGatewayDriver`.
- `src/Providers/TwilioServiceProvider.php` — registers the `twilio` driver on the manager.
- `config/laravel-sms-gateway-twilio.php` — provider credentials.
- `tests/Feature/TwilioDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- Read credentials via `$this->driverConfig('key')`, which resolves from
  `laravel-sms-gateway-twilio.*`.
- Build requests with `$this->request()` so shared timeouts and the `SmsSent`
  event stay in place.
- Keep the driver free of any dependency on sibling driver packages.
