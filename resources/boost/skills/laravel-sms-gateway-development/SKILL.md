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
- `Drivers/<package>/` — a full composer package: `composer.json`, `config/`,
  `src/<Ns>Driver.php`, `src/Providers/<Ns>ServiceProvider.php`, `tests/`.

## Rules

- Run tests and analysis from the monorepo root: `composer test`, `composer analyse`, `composer format`.
- Never edit a driver in its split repository; change it here.
- A new driver package needs: a directory under `Drivers`, an entry in the
  root `composer.json` `autoload-dev` and `suggest`, an entry in
  `.github/workflows/split-packages.yml`, a provider in `tests/TestCase.php`,
  and an exclusion in `phpstan.neon`.
- Keep every driver package free of any dependency on a sibling driver package.
