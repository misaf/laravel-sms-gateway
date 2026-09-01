# Repository Guidelines

## Project Structure & Module Organization

The root package provides the provider-neutral SMS gateway contract, manager, facade, events, and `null` driver under `src/`. Package configuration lives in `config/`, while Laravel Boost guidance lives in `resources/`. Pest tests are in `tests/Feature`, with shared setup in `tests/TestCase.php` and architecture checks in `tests/ArchTest.php`.

First-party driver integrations are independent packages under `Drivers/laravel-sms-gateway-{ghasedak,ippanel,kavenegar,magfa,melipayamak,messagebird,plivo,smsir,sunway,textlocal,twilio,vonage}`. Each owns its `composer.json`, source, configuration, tests, documentation, and license. Keep provider SDK and HTTP behavior — endpoints, credentials, timeouts, response mapping, and provider logging — inside its driver package. The core must remain provider-neutral and must not use `Illuminate\Http\Client` directly.

## Build, Test, and Development Commands

- `composer install` installs PHP 8.4 development dependencies.
- `composer test` runs the complete Pest suite; use `vendor/bin/pest --parallel` to match CI.
- `vendor/bin/pest tests/Feature/SmsGatewayManagerTest.php` runs a focused test file.
- `composer test-coverage` generates coverage output when a coverage driver is available.
- `composer analyse` runs PHPStan/Larastan at level 10 across `src/` and `Drivers/`.
- `composer format` applies the repository's Laravel Pint rules. Before a PR, prefer `vendor/bin/pint --dirty --format agent` for touched PHP files.

## Coding Style & Naming Conventions

Use strict types, PSR-4 namespaces, four-space indentation, and the rules in `pint.json`. Classes and enums use StudlyCase (`NullSmsGatewayDriver`); methods and variables use camelCase; configuration keys use snake_case. Match namespace ownership to directory layout. Depend on `Contracts\SmsGateway` across package boundaries instead of concrete provider implementations. A small amount of duplication between driver packages is preferred over a shared provider abstraction.

## Testing Guidelines

Tests use Pest 5 with Orchestra Testbench. Name files after the subject and suffix them with `Test.php`; write behavior-focused `it('...')` descriptions. Add feature coverage beside the owning package and update architecture tests when boundaries change. Run the smallest relevant test first, then the parallel full suite and static analysis. No fixed coverage percentage is configured, but changed behavior requires focused automated coverage.

## Commit & Pull Request Guidelines

Follow the repository's Conventional Commit style, including optional scopes: `feat(driver): add provider X`, `fix(twilio): resolve auth header`, or `refactor!: rename public API`. Keep commits focused.

PRs must complete `.github/PULL_REQUEST_TEMPLATE.md`: explain outcome and scope, identify public API, configuration, and cross-package effects, and list verification performed. Link relevant issues, document upgrade steps for breaking or manual changes, and update examples when public behavior changes. Screenshots are only needed for visual documentation changes.