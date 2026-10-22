# OTP and password-reset security acceptance — 22 October 2026

**Scope:** API OTP verification and password-reset flow only. Related work: #75 (P0 passkey), #78 (temporary passkey disablement), #85 (tenant token membership), #86 (proxy/RP origin).

## Verified baseline risks

- **P1 — password reset did not revoke outstanding Sanctum personal access tokens.** The original `PasswordOtpController::reset()` changed only the password; bearer tokens continued authenticating.
- **P1 — OTP consume was not an atomic row-locked verify/consume operation.** The original implementation verified and then queried/saved the active row separately. A concurrent request could read the same unconsumed code. Non-atomic attempt increments were also exposed to concurrency.
- **P1 — revoked/expired tenant membership not enforced for persisted tokens** is tracked independently in #85. Not changed by this patch.
- **P0 — legacy passkeys are not WebAuthn**, tracked in #75, with temporary mitigation #78. Not changed by this patch.

## Changes in this PR

1. Use a database transaction and `SELECT ... FOR UPDATE` when verifying or consuming OTP; validate and mark consumed on the same locked row. An unsuccessful attempt is accounted for while holding the lock.
2. On a **successful** password OTP reset, change the password and rotate the remember-me token, then delete all of the account's Sanctum personal access tokens in one outer transaction with OTP consumption.
3. Preserve token validity on invalid codes and retain existing response contracts and rate limits.

## Automated tests

- `test_consumed_otp_cannot_be_reused`
- `test_password_reset_revokes_existing_api_tokens_and_rotates_remember_token`
- `test_invalid_reset_code_preserves_existing_api_tokens`
- Existing valid/expired/max-attempt OTP and password-reset regression tests.

**Acceptance gate:** require the full pull-request GitHub Actions matrix including PHP 8.4 tests, PostgreSQL integration, static analysis, and quality checks to pass. These tests demonstrate sequential non-replay and revocation. **Simultaneous-request contention testing against PostgreSQL/MySQL remains a separate staging check**; no claim of runtime concurrency proof is made here.

## Deliberate exclusions / remaining risk

- Existing web sessions (not simply remember-me cookies) are not reliably revoked across all session drivers by rotating `remember_token`. Implement driver-appropriate user session invalidation with regression tests before declaring complete global logout.
- Password reset OTP delivery via email is not implemented by `LogOtpMessenger` (metadata logging only); this is a functional dependency, not a verified secure delivery channel.
- Token issuance policy and tenant membership revocation must be coordinated with Agent SaaS (#85).
- Do not enable or advertise WebAuthn/passkeys until #75 is fully implemented and browser/security acceptance passes.
