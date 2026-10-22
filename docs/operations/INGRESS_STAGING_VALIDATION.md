# PR #80 — Staging ingress and trusted-proxy acceptance sheet

**Issue:** #72 · **PR:** #80 · **State:** hold merge until actual Nginx/aaPanel + optional Cloudflare ingress is verified by the authorized operator.

The current `main` trusts all proxies via `trustProxies('*')`; PR #80 replaces this with the explicit `TRUSTED_PROXIES` allowlist. The change can fix spoofing but, if misconfigured, can break HTTPS detection or collapse many users into one IP bucket. A green unit test alone does not prove the network boundary.

## Record actual topology (no guesses)

- Staging and production origin: vhost identity, effective FPM listener/socket, Nginx `real_ip` settings and fastcgi `REMOTE_ADDR` mapping (keep private addresses in operator-only evidence).
- Actual immediate peer that PHP/Laravel sees at each ingress path (direct allowed? Nginx loopback? Cloudflare edge? load balancer?).
- Which forwarding headers Nginx **replaces** versus forwards unchanged; any upstream `CF-Connecting-IP` handling; whether the edge is IP-allowlisted.
- If Cloudflare is in use, origin firewall restrictions and controlled updates to authenticated Cloudflare CIDR list.
- Define `TRUSTED_PROXIES` with **only known immediate trusted peers**, using IP/CIDR. Never use `*`. Keep this setting private to each environment; do not commit origin IPs to Git.

## Test matrix (capture redacted timestamp, expected/observed, and sign-off)

| Test | Expected |
|---|---|
| Staging normal HTTPS through real ingress | `$request->isSecure() = true`; correct host, no redirect loop; session secure cookie retained |
| Staging HTTP redirect | Correct HTTPS redirect; no mixed-content/asset failures |
| Forged `X-Forwarded-For` from **untrusted** peer | Effective `$request->ip()` stays actual peer; no rate-limit bypass |
| Forged `X-Forwarded-Proto` from untrusted peer | Cannot force secure URL detection |
| Trusted proxy supplies sanitized `X-Forwarded-For` chain | Real effective client IP, not arbitrary header or shared edge IP |
| Two distinct clients behind ingress | Distinct IP-keyed auth/OTP buckets where intended |
| One client sends different spoofed headers | Cannot evade IP-keyed throttles |
| Webhooks/API CSRF scope and signature rules | No accidental broad exposure; test valid and invalid signature |
| `GET /up`, login/logout, CSRF form, Filament/Livewire, upload/download | Functional and no session cookie downgrade |
| Direct-origin request where origin should be restricted | Rejected by firewall or ingress; if direct allowed, forwarding headers ignored |
| Rollback rehearsal | Previous compatible release and proxy settings recover expected behavior |

Use a **temporary authenticated staging-only diagnostic endpoint or protected instrumentation** to capture only fields needed for the matrix: transport peer `REMOTE_ADDR`, effective `$request->ip()`, `$request->isSecure()`, host and sanitized presence/source of proxy headers. Never expose this endpoint publicly, never log raw cookies, bearer tokens, request payloads or personal data, and remove it after UAT.

## Source-level review actions for PR #80

1. Current test covers spoofing from **one untrusted peer** plus rejects `*` in the config. Add a trusted-ingress positive test, IPv6/CIDR cases, forwarded-host/proto safety, and IP-based rate limiter test using the real middleware/runtime behavior.
2. Verify the PHP/Symfony trusted-header bitmask appropriate to this ingress. Only trust headers actually supplied or overwritten by the trusted ingress.
3. Verify config cache rebuild and application restarts on staging; confirm config-file allowlist is applied **after** framework configuration bootstrap.
4. Review CI at PR head SHA and confirm tests pass against the target Laravel version.
5. Record signed test evidence and owner-approved .env transition plan, including separate rollback. **Do not merge PR #80 without this evidence.**

**Explicitly outside GitHub CI:** firewall and Cloudflare settings, real client IP propagation, production session behavior, and Nginx fastcgi/HTTPS configuration.
