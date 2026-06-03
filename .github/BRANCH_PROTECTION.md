# Alur kerja: PR lalu `main`

Standar untuk FoundationOS: **jangan push langsung ke `main`**. Buat branch → buka Pull Request → tunggu CI hijau → merge.

## Langkah developer (setiap fitur)

```bash
git checkout main
git pull origin main

git checkout -b cursor/nama-fitur-6d50   # atau feature/nama-fitur

# ... edit, commit ...
git add .
git commit -m "Deskripsi perubahan"
git push -u origin cursor/nama-fitur-6d50
```

Di GitHub:

1. Buka repo → **Pull requests** → **New pull request**
2. Base: `main` ← Compare: branch Anda
3. Isi judul & deskripsi → **Create pull request**
4. Tunggu centang hijau (Pint, Larastan, PHPUnit)
5. **Merge pull request** (squash atau merge commit, sesuai kebiasaan tim)
6. Lokal: `git checkout main && git pull origin main`

## Ringkasan

| Langkah | Di mana |
|---------|---------|
| Kode | Branch `cursor/...-6d50` atau `feature/...` |
| Review & CI | Pull Request ke `main` |
| Produksi / referensi stabil | `main` setelah merge |

Commit yang sudah ada di `main` tidak perlu di-PR ulang. Aturan ini berlaku untuk **perubahan berikutnya**.

---

# Branch protection (opsional, mengunci aturan di GitHub)

Kalau Anda ingin GitHub **menolak** push langsung ke `main`, aktifkan branch protection.

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
