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
- `SmsSendFailed`, dispatched when a gateway rejects a send, with the request
  and response. Because the retry policy is configured with `throw: false`,
  this is how a failed send is observed.
- `SmsSendUnreachable`, dispatched with the exception when the gateway is never
  reached — a connection error or a timeout — after which the exception
  surfaces to the caller.

- Per-driver `timeout.server`, `timeout.client`, `retry.times` and
  `retry.sleep_milliseconds` config keys, each with its own environment
  variable (e.g. `SMS_GATEWAY_TWILIO_RETRY_TIMES`) and its own default, so a
  single gateway can be tuned without changing the others. The driver packages
  no longer read `sms-gateway.defaults.*`, which now serves custom drivers
  only.

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
- **Breaking.** `SmsGatewayDriver` and the first-party drivers no longer give
  their `$serverTimeout`, `$clientTimeout`, `$retryTimes` and
  `$retrySleepMilliseconds` constructor arguments defaults. Every value is
  passed from the driver's config file, which stays the single place the
  numbers are written down. A custom driver that relied on the defaults must
  pass all four.
- **Breaking.** A driver credential that is configured but empty now throws an
  `InvalidArgumentException` at driver resolution, like an empty `base_url`.
  An `.env` key that is present but empty reaches the driver as an empty
  string, past the `env()` default, and previously produced a provider 401 —
  after the full retry budget — instead of a clear configuration error.
- **Breaking.** The Kavenegar, Plivo and Twilio `base_url` values no longer
  contain the account segment (the API key, auth id and account sid). The
  drivers now prepend it to the request path, so `base_url` stays a plain host
  prefix that can be pointed at a proxy or a sandbox.

## 1.0.0 - 2026-08-27

Initial release.
