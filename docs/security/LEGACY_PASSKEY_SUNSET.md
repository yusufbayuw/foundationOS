# Legacy passkey API — release-blocking security mitigation

**Date:** 2026-10-22
**Tracking:** [Issue #75](https://github.com/yusufbayuw/foundationOS/issues/75)

## Current posture

The prior `/api/v1/auth/passkeys/*` endpoints used a custom OpenSSL signed-message check. They did not verify standards-compliant WebAuthn attestation or assertions, including ceremony type, RP ID hash, origin, authenticator flags and counters. Passing a challenge signature is **not** equivalent to WebAuthn/passkey authentication.

All four endpoints now return HTTP **503** with `error.code = passkey_unavailable`, including options, registration and login. **There is no environment flag to re-enable the unsafe implementation.** Other supported authentication methods are unchanged. Existing `user_passkeys` rows are preserved for investigation and controlled migration; they are not accepted for authentication.

The older `PasskeyChallengeService` and data model remain temporarily for data migration/reference, but no HTTP endpoint calls the legacy service. Do not expose it under a different route.

## Mandatory conditions before enabling genuine passkeys

1. Select a maintained WebAuthn relying-party server library compatible with PHP 8.4/Laravel 13 and document its security maintenance.
2. Validate registration attestation and authentication assertions server-side: server-generated single-use challenges, expected origin and HTTPS, RP ID hash, clientDataJSON `type`, authenticatorData, user presence/verification, credential ownership, accepted algorithms, sign-counter/replay handling.
3. Store verified credential public keys and metadata in the library's required format; do not treat unverified legacy `public_key` rows as registered authenticators. Require fresh verified registration where migration cannot be proven safe.
4. Resolve an authorized **current tenant membership** before token minting, issue least-privilege tenant-scoped Sanctum tokens, and define a separate explicit service-token policy.
5. Add negative tests for origin/RP mismatch, replay, swapped credential, user verification flags and tenant revocation; test on real browser authenticators in staging.
6. Restore the endpoints only after full security review, green CI and acceptance tests, updating OpenAPI/client UI claims to match.

Do not mark passkeys as implemented or production-ready while #75 remains open.
