# Branch protection for `main` (required for production security)

Configure in GitHub: **Settings → Branches → Branch protection rules → Add rule** (or edit existing rule for `main`).

## Recommended settings

| Setting | Value |
|---------|--------|
| Branch name pattern | `main` |
| Require a pull request before merging | Enabled |
| Required approvals | 1 (adjust for your team) |
| Dismiss stale pull request approvals | Enabled |
| Require status checks to pass | Enabled |
| Require branches to be up to date | Enabled (recommended) |

## Required status checks

Add these workflow names (exact names from `.github/workflows/`):

- `Laravel Pint` (job in `static.yml`)
- `Larastan (level 1 + baseline, all GA modules)` (job in `static.yml`)
- `test` or the PHPUnit job name from `tests.yml` — open a recent PR on `main` to see the check names GitHub reports

Also enable if you use them:

- `lint-translations` (from `lint-translations.yml`)
- `lint-tenant-fields` (from `lint-tenant-fields.yml`)

## Notes

- Branch protection cannot be enforced from this repository alone; a repository admin must apply it in GitHub.
- Cloud agents can push to `main` only if protection is not enabled or bypass lists include the bot account.
- After enabling protection, all production deploys should go through PR + green CI.

## Production tenancy checklist (deploy)

1. `APP_ENV=production`
2. `TENANCY_API_REQUIRE_TENANT=true` (default)
3. `TENANCY_SCOPE_FAIL_CLOSED=true` or omit (defaults to `true` when `APP_ENV=production`)
4. Run smoke: `php artisan test --compact tests/Feature/ProductionTenancySecurityTest.php tests/Feature/ApiTenantTokenRequirementTest.php`
5. `php artisan config:cache` after env changes
