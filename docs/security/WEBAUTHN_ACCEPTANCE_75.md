# WebAuthn security acceptance — Issue #75

**Status:** NOT APPROVED FOR PRODUCTION. This branch contains an opt-in integration, not an accepted deployment.

## Architecture

- Native standards-oriented relying party: `laravel/passkeys` pinned by Composer and backed by `web-auth/webauthn-lib`.
- The first-party package uses WebAuthn attestation/assertion verification; do not replace it with custom signature verification.
- `user_passkeys` is legacy and **untrusted**. Its rows remain preserved. `passkeys` stores separately verified credentials; credentials **must be enrolled afresh**.
- The old `/api/v1/auth/passkeys/*` APIs **remain 503** in #78. Never bridge them to new session routes or enable old custom signing.
- New RP routes use `web` session guard and are **not** tenant API-token issuance endpoints.
- Install safely with `PASSKEYS_ENABLED=false`. Do not flip the setting before every gate below passes.

## Deployment dependencies (do not enable without approvals)

- **DevOps #86**: single explicit `PASSKEYS_ORIGIN=https://actual.login.host`; `PASSKEYS_RP_ID=actual.login.host`, canonical ingress and correctly scoped trusted proxy headers. Do not trust arbitrary Host or X-Forwarded-Host. HTTPS and secure cookies enforced, no wildcard origin.
- **SaaS #85**: user membership expiry/revocation, account suspension, platform-owner privileges, tenant-context session semantics and separate least-privilege service-principal tokens. WebAuthn session login alone must not grant tenant access or mint API tokens.
- **MFA #91**: establish whether WebAuthn session login satisfies Filament MFA policies or must still complete existing TOTP/email factors. A passkey must not bypass a second-factor requirement through an alternative route.
- **Credential audit**: make sure password fallback, recovery and account deletion remain operational. No trusted legacy import or silent migration.
- `composer.lock` and dependencies reviewed for advisories. Run migrations only after approval; new passkeys table is additive and does not alter legacy rows.

## Required positive security tests

- Register a real authenticator in a trusted HTTPS browser; store a library-verified credential in `passkeys`.
- Successful assertion logs into only the owning account; login regenerates the session and grants only authorized panel/tenant access.
- Multiple authenticators per user; credential rotation and removal; unknown credential denied.
- Verify tenant membership revocation, membership expiration and account lock before protected resource access.
- Perform recovery with password/MFA fallback; do not strand existing accounts.

## Required negative security tests

- Wrong clientDataJSON ceremony type, challenge, origin, expiry; replay of challenge or assertion.
- Wrong RP ID/rpIdHash, WebAuthn UP/UV flags, credential ownership or userHandle; swapped registered credential.
- Invalid COSE algorithm, malformed attestation, forged authenticatorData and invalid signature.
- Counter rollback or cloned authenticator (including zero-counter authenticators, per verified-library rules), concurrent replay under database row locks.
- Cross-user registration, cross-tenant session switch, suspended/revoked member, expired membership, stolen session and token use.
- Registration/login rate limits, secure cookies/CSRF, audit logs without exposing raw authenticator secrets.
- Test on at least two supported platform authenticators and a hardware security key in staging. Capture sanitized traces; no sensitive credential material in logs.

## Rollback and monitoring

- Rollback activation instantly by setting `PASSKEYS_ENABLED=false` and clearing cached configuration/routes with the normal deployment procedure.
- Do not delete `passkeys` or `user_passkeys` in an emergency rollback; disabling routes is enough.
- Monitor authenticated login failures, ceremony errors, tenant authorization failures and unexpected token issuance; rate-limit alerting and avoid raw payload logging.

## Release verdict

Until all checks pass with evidence in CI and staging, **P0 #75 remains open**. Passing route/config tests or green static analysis alone does not satisfy this checklist.
