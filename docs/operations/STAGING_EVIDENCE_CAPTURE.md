# Staging evidence capture — read-only, redacted

**Purpose:** Collect the minimum evidence for issues #72/#74 without assuming any actual aaPanel, Nginx, Cloudflare, PHP-FPM, database, Redis or cron topology. **Do not execute migration, restore, `queue:retry`, `queue:flush`, `optimize:clear`, `key:generate`, service restart or `nginx -s reload` while collecting evidence.**

## 1. Before collecting

Use a reviewed staging session with least privilege. Write unredacted machine/network details to a restricted operator-only change ticket. When sharing with a GitHub PR, report only boolean outcomes (pass/fail), sanitized service versions, effective client IP relation (equal/different, not actual IP), timing and non-sensitive route outcomes. Confirm `pwd` points to staging and PHP CLI binary points to the **same** runtime intended for the web app.

## 2. Read-only staging commands

Commands are examples to execute **one by one** from the verified FoundationOS directory; adapt service names and paths only after discovering actual installation. **Do not paste the raw output of commands displaying credentials or network details.**

```bash
pwd
git rev-parse HEAD
php -v
php artisan about
php artisan route:list --path=up
php artisan schedule:list
php artisan migrate:status
php artisan queue:failed
df -h
df -i
# Only if Nginx is really installed (this checks syntax without reloading):
# nginx -t
# Only if Supervisor is really used:
# supervisorctl status
# curl -fsS -o /dev/null -w 'HTTP status: %{http_code}\n' https://VERIFIED_STAGING_HOST/up
```

Report configured named queues and `retry_after` versus workers' `--timeout`, and whether the queue backend is DB or Redis, **without** reporting credentials. Confirm scheduler owner and `schedule:run` once per minute **or** one managed `schedule:work`, not both. Verify no accidental duplicate runs. Log the current PHP-FPM socket in the protected ticket.

## 3. Trusted ingress tests (#72 / PR #80)

Use [INGRESS_STAGING_VALIDATION.md](./INGRESS_STAGING_VALIDATION.md) for the test matrix. Diagnose `REMOTE_ADDR`, effective request `ip()`, `isSecure()`, chosen host and sanitized forwarded headers using a temporary **authenticated**, staging-only diagnostic placed behind firewall/IP restriction. Remove it after UAT. **Never** expose raw environment details on `/up`.

For a trusted edge, test two distinct clients and one repeat client; confirm legitimate client IP differentiation and no throttle evasion by spoofing forwarding headers. Also exercise an untrusted network peer attempting to supply `X-Forwarded-For` and `X-Forwarded-Proto`. Review Cloudflare origin restrictions and Nginx header sanitation **only if Cloudflare is present**.

Submit a table (PASS/FAIL + timestamp + redacted CI SHA) for: TLS redirect; HTTPS detection; secure/SameSite session cookie; login/CSRF; Filament Livewire request; public/private file access; health `/up`; untrusted forwarding rejection; trusted forwarding acceptance; OTP/auth rate limits; origin direct-access policy.

## 4. Queue and scheduler operations (#74)

Record: expected and observed scheduler tick frequency, owner/host identity (protected record), sample overdue jobs and oldest queue age, failed job count, retry/reconciliation permissions, Moodle `fos:moodle:ops-report --json` summarized status, helpdesk SLA, workflow escalation and billing job ownership. `--json` results may contain tenant or payload data; share only summarized/redacted observations. Do not replay jobs until a module owner proves idempotency.

## 5. Backup drill (#74)

Record the source database engine/version, private/public/external storage inventory, backup timestamp, integrity/checksum result, encryption key custody, offsite transfer confirmation, **separate destination DB and storage**, restore command exit codes, representative integrity checks for tenant isolation and private files, and elapsed recovery time. Target RPO and RTO must be *approved* by Project Owner, and achieved RPO/RTO must be *measured* in the drill. A CI synthetic fixture restoration does not qualify as a staging recovery drill.

**Never run a restore to a live DB or use actual service integration secrets on staging.** Disable outbound payment/Moodle/mail/webhooks for the restored environment and preserve the originals.

## 6. Evidence signoff

| Gate | Result/date | Evidence ticket | Verifier |
|---|---|---|---|
| Candidate SHA + mandatory CI | TODO | TODO | TODO |
| Real ingress + proxy UAT | TODO | TODO | TODO |
| Tenant/finance/education/operations module smoke | TODO | TODO | TODO |
| Backups encrypted/offsite + isolated real restore | TODO | TODO | TODO |
| RPO/RTO approved and measured | TODO | TODO | TODO |
| Worker/scheduler/alerts verified | TODO | TODO | TODO |
| Schema migration and rollback rehearsal | TODO | TODO | TODO |
| Project Owner GO / NO-GO | TODO | TODO | TODO |

**Any missing P0 evidence is a release NO-GO.**
