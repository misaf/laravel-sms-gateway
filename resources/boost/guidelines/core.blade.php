## Laravel SMS Gateway

The `misaf/laravel-sms-gateway` package provides a provider-neutral, driver-based SMS gateway manager for Laravel applications.

### Standards

- Keep core code inside the package root using the `Misaf\LaravelSmsGateway` namespace.
- This package owns the `SmsGateway` contract, `SmsGatewayDriver`, `SmsGatewayManager`, the `SmsGateway` facade, the `SmsSent` event, and `NullSmsGatewayDriver`.
- Keep the core package standalone. Never import a concrete SMS provider or a sibling driver package from core.
- Provider packages depend on this package and register drivers through `SmsGatewayManager::extend()`; the dependency must never point from core to provider.
- Keep the `null` driver as the provider-neutral default. It makes no external request and reports success.
- Drivers must extend `SmsGatewayDriver` and build every request with `$this->request()` so the shared timeouts and the `SmsSent` event stay in place.
- Read credentials with `$this->driverConfig('key')`, which resolves from `laravel-sms-gateway-{driver}.*`. Never read `config/services.php`.
- Never instantiate a driver directly; resolve it through the manager so its driver name is injected.
- Keep focused Pest coverage for driver registration, request shaping, config overrides, and the `SmsSent` event.
- Keep the architecture presets plus `arch()->expect('Misaf\LaravelSmsGateway')->not->toUse([...])`.
