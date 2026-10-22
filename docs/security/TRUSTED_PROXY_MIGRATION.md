# Trusted proxy migration — aaPanel/Nginx/Cloudflare

**Date:** 2026-10-22  
**Tracking:** [#72](https://github.com/yusufbayuw/foundationOS/issues/72)

Previously, bootstrap trusted every remote address via `trustProxies('*')`. In ingress setups where arbitrary clients can connect to the origin or influence `X-Forwarded-For`, this can compromise IP-keyed rate limiting for auth/OTP/webhook routes.

## Configuration

The new `config/trustedproxy.php` is read by Laravel's built-in TrustProxies middleware at request time (after configuration has bootstrapped), and reads `TRUSTED_PROXIES`, a comma-separated allowlist of **actual immediate proxy peers**, in IP or CIDR notation. Empty means no forwarded-header trust. Wildcard `*` is rejected explicitly.

Example for a locally co-located reverse proxy (only if it is the immediate HTTP peer):

```dotenv
TRUSTED_PROXIES=127.0.0.1,::1
```

If Nginx receives Cloudflare requests directly, determine whether Laravel's `REMOTE_ADDR` is the Nginx peer, Cloudflare edge IP, or the original client before choosing any list. If Cloudflare networks are involved, use the currently published IP ranges under a controlled update process, restrict direct origin access, configure Nginx `real_ip` only for authenticated known Cloudflare ingress, and ensure Nginx strips/replaces client-supplied forwarding headers.

## Staging acceptance (mandatory before production merge)

1. Capture `REMOTE_ADDR`, effective `$request->ip()`, `$request->isSecure()`, host, and actual forwarded headers **without logging credentials, cookies or personal data**.
2. Test traffic through normal aaPanel/Nginx and, if enabled, Cloudflare; ensure HTTPS redirects, sessions, URL generation, CSRF and health `/up` all work.
3. Send forged `X-Forwarded-For` and `X-Forwarded-Proto` requests directly to origin and via intended ingress. For an untrusted peer, spoofing must have no effect.
4. Test auth/OTP/webhook rate limits: different client IPs must not incorrectly share a Cloudflare edge limit, while the same client cannot bypass limits by changing forwarded headers.
5. Set the exact proxy list in production `.env`, rebuild configuration cache, monitor client-IP and redirect metrics, and retain immediate rollback instructions.
6. Never commit private origin addresses, passwords, or secrets to this repository.

**Do not merge/deploy this configuration change before validating the actual ingress topology.** It changes how client IPs and HTTPS are detected behind reverse proxies. The PR is intentionally a draft pending network/UAT evidence.
