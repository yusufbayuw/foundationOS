# Historical CI APP_KEY reuse verification — #122

**Date:** 2026-10-25  
**Status:** Key exposure in public Git history confirmed; **production/staging equivalence NOT VERIFIED**. Do not close #122 on Git-only evidence.

## Verified evidence (without disclosing any secret)

- The old workflow at the commit immediately before PR #120 contained a literal Laravel `APP_KEY`.
- Current `main` workflows instead generate a random key per CI execution; the static key is no longer present in that tested workflow.
- The public Git revision remains accessible in history after the mitigation. Removing the current assignment is not key revocation and does not prove whether live instances use the exposed value.
- No production/staging environment variables or encryption keys have been retrieved for this report.

## Authorized operator procedure — do not share any keys

1. Record each environment **privately**: production, staging, pre-production, worker pool, and any independently deployed instance. Verify the exact active release path and account executing PHP-FPM and queue workers. Do not attach `.env`, a config dump, log snippet, screenshots or a fingerprint.
2. On the machine, from the *active checkout with full Git history*, run:
   `php scripts/security/verify-historical-ci-app-key.php`
   The read-only script reads the historical public CI key in memory and compares it using `hash_equals` against the local Laravel application key obtained from the CLI-bootstrapped config. It emits only `MATCH`, `NO MATCH`, or `NOT VERIFIED`. Exit codes: 2=match, 0=no match, 3=not verified. **Never paste output from `php artisan config:show app` into tickets.**
3. Make sure the script's PHP CLI environment and active `bootstrap/cache/config.php` correspond to running PHP-FPM/worker processes; a `NO MATCH` from the wrong release, a fresh CLI process or a different key source is **not sufficient** for production clearance. If cache/workers differ, classify `NOT VERIFIED` until reconciled.
4. Record **only** environment label, date/time, classification, executor/approver (internally), and whether FPM/queue runtime was reconciled. Never store or publicly post the raw key, its partial text or its hash.
5. Repeat on each environment. If history is shallow, no key can be loaded, the application cannot bootstrap, or the key source is uncertain, output `NOT VERIFIED`; obtain security-owner assistance through a private channel.

## Decision matrix

| Result | Security impact | Required action |
| --- | --- | --- |
| NO MATCH across **all** active environments, runtime reconciled | Exposed disposable CI test secret; no evidence of live-key reuse | Document scope and confirm no persistent encrypted data/fixtures/CI services reused it. No production rotation required solely because of this finding. |
| MATCH on any environment | Assume the matching deployed encryption key is compromised | Declare incident, hold rollout, preserve an encrypted/verified backup, inventory encrypted data and secrets, and obtain controlled rotation/re-encryption approval. |
| NOT VERIFIED for any environment | Scope undetermined | Keep P0 open and release blocked pending operator validation. |

## Rotation plan **only if a MATCH is confirmed**

- Inventory encrypted DB fields, encryption-cast models, secure settings, queued jobs/payloads, cookies and sessions, remember-me tokens, signed/cached URLs, backup secrets and dependent third-party integrations before altering a key.
- Back up and verify decryption of a representative encrypted record under an offline restoration rehearsal. Restrict backup/key handling to authorized operators.
- Design key rollover/old-key handling compatible with the exact Laravel version (including `APP_PREVIOUS_KEYS` **only if** supported and authorized). Establish an explicit re-encryption and old-key retirement plan; using fallback keys forever does not end compromise.
- Invalidate all sessions and sensitive tokens, rotate other exposed secrets if evidence shows reuse, restart FPM and all workers with verified consistent config, and audit logs for suspicious access.
- Maintain a non-destructive rollback for data re-encryption and a validated secure restore. Never blindly execute `php artisan key:generate` on a populated environment: it can render existing encrypted data unreadable.
- Record incident decision and private evidence; PM, Security, QA and authorized infrastructure operator must approve.

## Security limits and governance

- `composer audit` / CVE-2026-114509 is independently tracked at #121. Passing this historical-key check does not remove the Laravel release blocker.
- Public Git history may also appear in forks and caches; history rewrite alone is not a substitute for revoking any deployed matching key.
- Keep the results table private if environment architecture is sensitive. Public GitHub comments should contain only aggregated closure state without identifiable secrets or fingerprints.
- The script is **read-only**. It performs no key generation, rotation, log inspection, database modification, production deployment or network call.

**Acceptance:** All deployed instances `NO MATCH` with runtime verification, or documented completed controlled rotation for every `MATCH`; no `NOT VERIFIED` remaining; QA + Security + PM signoff.
