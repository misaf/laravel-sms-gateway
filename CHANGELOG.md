# Changelog

All notable changes to `misaf/laravel-sms-gateway` and its first-party
driver packages are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- `Misaf\LaravelSmsGateway\Drivers\SmsGatewayDriver`, a shared abstract base
  that owns the timeouts, the retry policy and the send events. Every
  first-party HTTP driver now extends it and implements `name()`,
  `sendRequest()` and, when it has credentials, `configure()`.
- `SmsSending`, dispatched before a send attempt with the driver name and the
  payload.
- `SmsSendFailed`, dispatched when a gateway rejects a send (with the request
  and response) or is unreachable (with the exception). Because the retry
  policy is configured with `throw: false`, this is how a failed send is
  observed.

### Changed

- `SmsSent` is now dispatched only for a successful response. Previously it
  fired for every response, including 4xx and 5xx.
- **Breaking.** A driver's default base URL now lives in its published config
  file (`sms-gateway-{driver}.base_url`) instead of a `DEFAULT_BASE_URL` class
  constant, so it is a single source of truth that an application can edit.
  The `defaultBaseUrl()` driver hook is gone, and an empty `base_url` now
  throws an `InvalidArgumentException` at driver resolution instead of sending
  to a relative URL. A config published before this release keeps its empty
  default and must be republished or given a value.
- **Breaking.** The Kavenegar, Plivo and Twilio `base_url` values no longer
  contain the account segment (the API key, auth id and account sid). The
  drivers now prepend it to the request path, so `base_url` stays a plain host
  prefix that can be pointed at a proxy or a sandbox.

## 1.0.0 - 2026-08-27

Initial release.
