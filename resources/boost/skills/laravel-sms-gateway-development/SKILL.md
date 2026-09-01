---
name: laravel-sms-gateway-development
description: Guidance for developing the misaf/laravel-sms-gateway monorepo and its SMS driver packages.
---

# laravel-sms-gateway development

This repository is a monorepo. The core package lives at the root; each provider
driver lives in `Drivers/laravel-sms-gateway-<driver>` and is split out to
its own read-only repository on release by `.github/workflows/split-packages.yml`.

## Layout

- `src/Contracts/SmsGateway.php` — the contract every gateway implements.
- `src/SmsGatewayManager.php` — resolves drivers; ships only the `null` driver. It is
  the only thing the core provider binds (plus the `sms-gateway` alias); the contract
  itself is never bound in the container.
- `src/Facades/SmsGateway.php`, `src/Providers/SmsGatewayServiceProvider.php`.
- `src/Drivers/NullSmsGatewayDriver.php` — the provider-neutral default.
- `src/Drivers/SmsGatewayDriver.php` — the shared HTTP base: timeouts, retry
  policy, send events, and `requireConfigured()`, the guard every driver uses on
  its base URL and credentials.
- `Drivers/<package>/` — a full composer package: `composer.json`, `config/`,
  `src/<Ns>Driver.php`, `src/Providers/<Ns>ServiceProvider.php`, `tests/`.

## Rules

- Run tests and analysis from the monorepo root: `composer test`, `composer analyse`, `composer format`.
- Never edit a driver in its split repository; change it here.
- A new driver package needs: a directory under `Drivers`, an entry in the
  root `composer.json` `autoload-dev` and `suggest`, an entry in
  `.github/workflows/split-packages.yml`, a provider in `tests/TestCase.php`,
  and an exclusion in `phpstan.neon`.
- Give a driver constructor no default values: the base URL, the credentials,
  the timeouts and the retry settings are all passed from the driver's config
  file, which is the only place a value is written down.
- Guard every value a driver cannot work without — its base URL and each
  credential — with `self::requireConfigured($value, '<label>')` in the
  constructor, and cover it with a test. A config key that is present but empty
  passes `Config::string()`, so it has to be rejected here.
- Keep every driver package free of any dependency on a sibling driver package.
