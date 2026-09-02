# Changelog

All notable changes to `misaf/laravel-sms-gateway` and its first-party
driver packages are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## 2.0.0 - 2026-09-02

### Added
- Shared `SmsGatewayDriver` HTTP base for drivers with connect/request timeouts, a `throw: false` retry policy (connection errors and 5xx), and the `SmsSendFailed`, `SmsSending`, and `SmsSendUnreachable` lifecycle events
- `requireConfigured()` credential guard that rejects base URLs and credentials that are present but empty
- Deferred, order-independent driver registration via `callAfterResolving()`
- Per-driver `timeout.*` and `retry.*` config defaults with a server/client timeout split
- Named `Response` constants replacing magic-number HTTP status codes

### Changed
- Drivers implement `Contracts\SmsGateway` via the shared `SmsGatewayDriver` base instead of the removed single driver base class
- Config file and keys renamed from `laravel-sms-gateway` to `sms-gateway`; `sms-gateway.defaults.*` is now the fallback for custom drivers
- Resolve drivers with `driver()` instead of the removed `gateway()`; the provider binds only the manager and its `sms-gateway` alias, never the contract
- Renamed `name()` to `driverName()` on the shared base

### Fixed
- Fall back to the null driver when the default driver key is configured but empty


## [1.0.0] - 2026-08-27

Initial release.
