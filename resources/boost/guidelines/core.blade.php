## Laravel SMS Gateway

The `misaf/laravel-sms-gateway` package provides a provider-neutral, driver-based SMS gateway manager for Laravel applications.

### Standards

- Keep core code inside the package root using the `Misaf\LaravelSmsGateway` namespace.
- This package owns the `SmsGateway` contract, `SmsGatewayManager`, the `SmsGateway` facade, the `SmsSent` event, and `NullSmsGatewayDriver`.
- Keep the core package standalone. Never import a concrete SMS provider or a sibling driver package from core.
- Provider packages depend on this package and register drivers through `SmsGatewayManager::extend()`; the dependency must never point from core to provider.
- The service provider binds only the `SmsGatewayManager` singleton and its `sms-gateway` alias. Never bind the `SmsGateway` contract in the container; gateways are resolved through `SmsGateway::driver()` or `SmsGatewayManager::driver()`.
- Keep the `null` driver as the provider-neutral default. It makes no external request and reports success.
- Drivers are `final` classes implementing `Contracts\SmsGateway`. There is no shared abstract driver: each one builds its own `PendingRequest` and dispatches `SmsSent` itself. A little duplication between drivers is preferred over a shared base class.
- Drivers take their credentials and timeouts as constructor arguments. The package service provider reads them with `Config::string()`/`Config::integer()` from `sms-gateway-{driver}.*` and `sms-gateway.defaults.*`. Never read `config/services.php`.
- Register drivers with `$manager->extend('{driver}', fn (): SmsGateway => new {Driver}(...))` inside `callAfterResolving(SmsGatewayManager::class, ...)`, so runtime config changes are honoured.
- Keep focused Pest coverage for driver registration, request shaping, config overrides, and the `SmsSent` event.
- Keep the architecture presets plus `arch()->expect('Misaf\LaravelSmsGateway')->not->toUse([...])`.
