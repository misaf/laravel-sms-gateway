---
name: laravel-sms-gateway-plivo-development
description: Guidance for developing the misaf/laravel-sms-gateway-plivo package, the Plivo driver for Laravel SMS Gateway.
---

# laravel-sms-gateway-plivo development

This package is developed inside the `misaf/laravel-sms-gateway` monorepo at
`Drivers/laravel-sms-gateway-plivo` and split out to its own read-only repository on release.

## Layout

- `src/PlivoDriver.php` — a `final` driver implementing `Misaf\LaravelSmsGateway\Contracts\SmsGateway`.
- `src/Providers/PlivoServiceProvider.php` — registers the `plivo` driver on the manager.
- `config/sms-gateway-plivo.php` — provider credentials.
- `tests/Feature/PlivoDriverTest.php` — run from the monorepo root with `composer test`.

## Rules

- Never edit files here in the split repository; change them in the monorepo.
- The driver takes its credentials and timeouts as constructor arguments; the
  service provider reads them all from `sms-gateway-plivo.*`, timeouts and retry included.
- The driver extends `Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver`, which
  owns the timeouts, the retry policy and the `SmsSending`/`SmsSent`/
  `SmsSendFailed`/`SmsSendUnreachable` events. Implement `name()`, `sendRequest()`
  and, for credentials, `configure()`. The base URL comes from the config file,
  which is the only place it is defined.
- Guard the base URL and each credential with `self::requireConfigured()` in the
  constructor: a config key that is present but empty passes `Config::string()`,
  so it is rejected at driver resolution and covered by a test.
- Retry only connection failures and gateway 5xx responses; a rejected
  credential or a malformed payload must fail on the first attempt.
- Keep the driver free of any dependency on sibling driver packages.
