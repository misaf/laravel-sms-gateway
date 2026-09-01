# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

Run everything from the monorepo root — the root install owns all dependencies, and the root `composer.json` `autoload-dev` maps every driver's `src/` and `tests/`, so driver tests only run from here.

```bash
composer test                    # Pest (all suites)
vendor/bin/pest --parallel       # what CI runs
composer analyse                 # PHPStan/Larastan, level 10
composer format                  # Pint (per preset)
vendor/bin/pint --dirty --format agent
composer validate --strict
```

Focused runs:

```bash
vendor/bin/pest tests/Feature/SmsGatewayManagerTest.php     # one file
vendor/bin/pest --filter 'part of the test name'            # one test
vendor/bin/pest Drivers/laravel-sms-gateway-twilio/tests    # one driver
vendor/bin/pest --testsuite Architecture
```

PHP 8.4, Laravel 13. `phpunit.xml` defines two suites: **Feature** (`tests/Feature`, `Drivers/*/tests/Feature`, `Drivers/*/tests/Registration`) and **Architecture** (`*ArchTest.php`).

## Architecture

A monorepo: the provider-neutral core is at the root (`Misaf\LaravelSmsGateway\` → `src/`), and each provider driver is a full composer package under `Drivers/laravel-sms-gateway-<driver>`, split out to its own **read-only** repository by `.github/workflows/split-packages.yml`. Never edit a driver in its split repo; change it here. Releases go through Symplify MonorepoBuilder (`monorepo-builder.php`, default branch `1.x`).

**Core.** `SmsGatewayManager` extends `Illuminate\Support\Manager`; `getDefaultDriver()` reads `sms-gateway.default`, and the only driver it ships is `null` (`NullSmsGatewayDriver`, which implements the `SmsGateway` contract directly and fakes a 200 response). The service provider binds *only* the manager plus its `sms-gateway` alias — the `SmsGateway` contract is deliberately not bound, so type-hinting it for injection will not resolve. Resolve through the facade or the manager.

**`Drivers/SmsGatewayDriver`** is the shared HTTP base and owns everything cross-cutting: the `Http::baseUrl()` client, connect/request timeouts, the retry policy (`throw: false`, retrying only connection errors and 5xx), the four lifecycle events, and `requireConfigured()`. A concrete driver supplies only `driverName()`, `sendRequest()`, and optionally `configure()` for auth. Because retries are configured with `throw: false`, a rejected send returns a response rather than throwing — `SmsSendFailed` is how a failure is observed; `SmsSendUnreachable` fires just before a connection error surfaces.

**Driver self-registration is order-independent, and that is load-bearing.** Each driver's provider registers via `$this->callAfterResolving(SmsGatewayManager::class, fn ($manager) => $manager->extend('<name>', ...))`. Resolving the manager during registration would build a throwaway manager whenever the driver package is discovered before the core one, silently losing the driver. Every driver therefore carries both a `tests/TestCase.php` (core provider first) and a `tests/ReversedOrderTestCase.php` (driver provider first) with matching tests under `tests/Feature` and `tests/Registration`.

### Conventions

- Keep the core provider-neutral: it must not depend on a provider SDK, and the arch test asserts no core namespace uses any driver namespace. Drivers depend on `Contracts\SmsGateway` / the base class, never on a sibling driver.
- Prefer a little duplication between driver packages over a shared provider abstraction. Endpoints, credentials, timeouts, and response mapping belong to the owning driver.
- Driver constructors take **no default values** — base URL, credentials, timeouts, and retry settings all come from the driver's own config file, the single place a value is written down. First-party drivers own their `timeout.*` / `retry.*` keys and `SMS_GATEWAY_<DRIVER>_*` env vars; `sms-gateway.defaults.*` is the fallback for custom drivers only.
- Guard the base URL and every credential with `self::requireConfigured($value, '<label>')` in the constructor and cover it with a test: a config key that is present but empty passes `Config::string()`, so it must be rejected there.
- Test cases call `Http::preventStrayRequests()` in `setUp`; payloads pass through to the provider untouched.
- Strict types, four-space indent, Conventional Commit messages (`fix(driver): …`, `docs(ghasedak): …`). Update the relevant README and `CHANGELOG.md` when public behavior, config, or upgrade requirements change.

### Adding a driver package

A new driver needs: the directory under `Drivers/`, entries in the root `composer.json` `autoload-dev` and `suggest`, an entry in the `split-packages.yml` matrix, its test cases registered in `tests/Pest.php`, and a tests exclusion in `phpstan.neon`.
