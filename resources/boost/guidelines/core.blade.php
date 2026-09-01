## Laravel SMS Gateway

The `misaf/laravel-sms-gateway` package provides a provider-neutral, driver-based SMS gateway manager for Laravel applications.

### Standards

- Keep core code inside the package root using the `Misaf\LaravelSmsGateway` namespace.
- This package owns the `SmsGateway` contract, `SmsGatewayManager`, the `SmsGateway` facade, the `SmsSending`/`SmsSent`/`SmsSendFailed` events, the abstract `Drivers\SmsGatewayDriver`, and `NullSmsGatewayDriver`.
- Keep the core package standalone. Never import a concrete SMS provider or a sibling driver package from core.
- Provider packages depend on this package and register drivers through `SmsGatewayManager::extend()`; the dependency must never point from core to provider.
- The service provider binds only the `SmsGatewayManager` singleton and its `sms-gateway` alias. Never bind the `SmsGateway` contract in the container; gateways are resolved through `SmsGateway::driver()` or `SmsGatewayManager::driver()`.
- Keep the `null` driver as the provider-neutral default. It makes no external request and reports success.
- HTTP drivers are `final` classes extending `Drivers\SmsGatewayDriver`, which owns the timeouts, the retry policy and the events. A driver implements `name()`, `sendRequest()` and, when it has credentials, `configure()`; its base URL comes from its config file. A driver that makes no HTTP request implements `Contracts\SmsGateway` directly.
- Drivers take their credentials and timeouts as constructor arguments. The package service provider reads them with `Config::string()`/`Config::integer()` from `sms-gateway-{driver}.*`; each driver config carries its own timeout and retry keys, which fall back to the core `SMS_GATEWAY_*` variables in `sms-gateway.defaults.*`. Never read `config/services.php`.
- Register drivers with `$manager->extend('{driver}', fn (): SmsGateway => new {Driver}(...))` inside `callAfterResolving(SmsGatewayManager::class, ...)`, so runtime config changes are honoured.
- Keep focused Pest coverage for driver registration, request shaping, config overrides, and the send events.
- Keep the architecture presets plus `arch()->expect('Misaf\LaravelSmsGateway')->not->toUse([...])`.
