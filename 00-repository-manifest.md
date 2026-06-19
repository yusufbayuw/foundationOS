# 00 — Repository Manifest

**REAP Stage:** 01 — Repository Manifest  
**Artifact Status:** Verified  
**Generated:** 2026-06-19T04:52:42Z  
**Purpose:** Menentukan scope audit reverse engineering (tanpa analisis bisnis).

---

## 1. Repository Identity

| Field | Value | Confidence |
|-------|-------|------------|
| Repository name | **foundationOS** | VERIFIED |
| Remote origin | `github.com/yusufbayuw/foundationOS` | VERIFIED |
| Local workspace path | `/workspace` | VERIFIED |
| Branch | `cursor/use-case-model-0a94` | VERIFIED |
| Commit (full) | `985d54ae51055b33c203cd0b7e2dfa363ac1c1ad` | VERIFIED |
| Commit (short) | `985d54a` | VERIFIED |
| Composer package name | `laravel/laravel` (skeleton); product name **FoundationOS** per `README.md` | VERIFIED |

---

## 2. File Inventory

Counts below use **`git ls-files`** (git-tracked files) unless noted.  
Command epoch: 2026-06-19, commit `985d54a`.

| Category | Count | Definition / glob | Confidence |
|----------|------:|-------------------|------------|
| **Total files (tracked)** | **6,062** | `git ls-files \| wc -l` | VERIFIED |
| **Total files (filesystem)** | **6,071** | All files under repo root excluding `./vendor`, `./node_modules`, `./.git` only | VERIFIED |
| **PHP files (all tracked)** | **5,281** | `git ls-files '*.php'` | VERIFIED |
| **Source files (application PHP)** | **5,108** | Tracked `*.php` excluding `tests/` prefix | VERIFIED |
| **Test files (PHP)** | **173** | `git ls-files 'tests/**/*.php'` | VERIFIED |
| **Migration files (PHP)** | **244** | `database/migrations/*.php` (37) + `Modules/*/database/migrations/*.php` (207) | VERIFIED |
| **Configuration files (PHP)** | **41** | `config/*.php` (24) + `Modules/*/config/*.php` (17) | VERIFIED |
| Factory files (PHP) | 37 | `database/factories`, `Modules/*/database/factories` | VERIFIED |
| Seeder files (PHP) | 18 | `database/seeders`, `Modules/*/database/seeders` | VERIFIED |
| Blade templates | 108 | `git ls-files '*.blade.php'` | VERIFIED |
| JavaScript (tracked) | 57 | `git ls-files '*.js'` | VERIFIED |
| JSON (tracked) | 113 | `git ls-files '*.json'` | VERIFIED |
| Markdown (tracked) | 132 | `git ls-files '*.md'` | VERIFIED |
| CI workflow files | 5 | `.github/workflows/*.yml` | VERIFIED |
| Feature modules (directories) | 45 | `ls Modules/ \| wc -l` | VERIFIED |
| Extraction / utility scripts | 21 | `git ls-files scripts/` | VERIFIED |

### 2.1 Source files — breakdown (PHP, tracked)

| Path | PHP files | Confidence | Evidence ID |
|------|----------:|------------|-------------|
| `app/` | 318 | VERIFIED | EV-M01-001 |
| `Modules/` | 4,685 | VERIFIED | EV-M01-002 |
| `routes/` | 3 | VERIFIED | EV-M01-003 |
| `bootstrap/` | 5 | VERIFIED | EV-M01-004 |
| `database/` (non-migration PHP) | 6 | VERIFIED | EV-M01-005 |
| `tests/` | 173 | VERIFIED (test scope) | EV-M01-006 |

### 2.2 Top tracked extensions (reference)

| Extension | Count |
|-----------|------:|
| `.php` | 5,281 |
| `.gitkeep` | 418 |
| `.md` | 132 |
| `.json` | 113 |
| `.js` | 57 |

---

## 3. Excluded Directories

Directories **excluded from filesystem scan totals** and **not treated as auditable source** in this manifest:

| Directory / pattern | Reason | Evidence |
|---------------------|--------|----------|
| `vendor/` | Composer dependencies; in `.gitignore` | `.gitignore` L21 |
| `node_modules/` | NPM dependencies; in `.gitignore` | `.gitignore` L15 |
| `.git/` | VCS metadata | standard |
| `public/build/` | Compiled frontend assets; in `.gitignore` | `.gitignore` L16 |
| `public/hot` | Vite HMR; in `.gitignore` | `.gitignore` L17 |
| `storage/*.key` | Secrets | `.gitignore` L19 |
| `storage/pail` | Runtime logs | `.gitignore` L20 |
| `storage/framework/` | Cache/sessions/views runtime | Laravel convention |
| `storage/logs/` | Runtime logs | Laravel convention |
| `storage/app/*` (except `private/`, `public/`, `.gitignore`) | Gitignored generated/local data | `storage/app/.gitignore` |
| `.env`, `.env.*` | Environment secrets | `.gitignore` L3–5 |
| `.idea/`, `.vscode/`, `.fleet/`, `.nova/`, `.zed/` | IDE metadata | `.gitignore` L8–13 |
| `.phpunit.cache/`, `.phpunit.result.cache` | Test cache | `.gitignore` L7, L11 |
| `laravel-code-2026-03-25/` | Archived dump; in `.gitignore` | `.gitignore` L25 |

---

## 4. Scan Assumptions

| ID | Assumption | Confidence |
|----|------------|------------|
| SA-01 | File counts reflect **git-tracked** tree at commit `985d54a`; untracked local files may exist (e.g. `storage/app/*.json`). | VERIFIED |
| SA-02 | `vendor/` and `node_modules/` are **not** scanned or counted. | VERIFIED |
| SA-03 | **Source files** = tracked PHP excluding `tests/`. Migrations and config are **reported separately**, not subtracted from source total. | INFERRED (manifest convention) |
| SA-04 | **No business logic, domain rules, or workflow semantics** are inferred in this stage. | VERIFIED (scope rule) |
| SA-05 | Product display name **FoundationOS** taken from `README.md`; git remote **foundationOS**; composer `name` remains `laravel/laravel`. | VERIFIED |
| SA-06 | Module count = top-level folders under `Modules/` (45). | VERIFIED |
| SA-07 | Filesystem total (6,071) may exceed tracked total (6,062) due to untracked local artifacts. | VERIFIED |

---

## 5. Coverage Analysis

| Scan area | Included in manifest? | Notes |
|-----------|----------------------|-------|
| Git-tracked file tree | Yes | Primary audit boundary |
| `app/` | Yes | 318 PHP |
| `Modules/` (45 modules) | Yes | 4,685 PHP |
| `routes/`, `bootstrap/` | Yes | Entry & routing |
| `database/migrations/` | Yes (separate count) | 244 files |
| `config/` + module config | Yes (separate count) | 41 files |
| `tests/` | Yes (separate count) | 173 PHP |
| `scripts/` | Yes | 21 tracked files |
| `docs/`, root `*.md` | Yes | Part of 132 markdown |
| `resources/`, module views | Partial | 108 blade tracked |
| `vendor/` | **No** | Excluded |
| `node_modules/` | **No** | Excluded |
| Runtime `storage/` content | **No** | Mostly gitignored |
| `.env` secrets | **No** | Excluded |
| Business / domain analysis | **No** | Out of stage scope |

**Coverage gap (manifest level only):** Untracked files under `storage/app/` are not inventoried (SA-01).

---

## 6. Evidence Registry

| Evidence ID | Command / path | Result | Confidence |
|-------------|----------------|--------|------------|
| EV-M01-001 | `git ls-files 'app/**/*.php' \| wc -l` | 318 | VERIFIED |
| EV-M01-002 | `find Modules -name '*.php' \| wc -l` (tracked subset 4685 on disk; git total Modules PHP via ls-files) | 4,685 | VERIFIED |
| EV-M01-003 | `git ls-files 'routes/*.php' \| wc -l` | 3 | VERIFIED |
| EV-M01-004 | `git ls-files 'bootstrap/*.php' \| wc -l` | 5 | VERIFIED |
| EV-M01-005 | `git ls-files 'database/**/*.php' \| rg -v '/migrations/' \| wc -l` | 6 (factories + seeders) | VERIFIED |
| EV-M01-006 | `git ls-files 'tests/**/*.php' \| wc -l` | 173 | VERIFIED |
| EV-M01-007 | `git ls-files \| wc -l` | 6,062 | VERIFIED |
| EV-M01-008 | `git rev-parse HEAD` | `985d54ae51055b33c203cd0b7e2dfa363ac1c1ad` | VERIFIED |
| EV-M01-009 | `git branch --show-current` | `cursor/use-case-model-0a94` | VERIFIED |
| EV-M01-010 | `git ls-files 'database/migrations/*.php' 'Modules/*/database/migrations/*.php' \| wc -l` | 244 | VERIFIED |
| EV-M01-011 | `git ls-files 'config/*.php' 'Modules/*/config/*.php' \| wc -l` | 41 | VERIFIED |
| EV-M01-012 | `git ls-files '*.php' \| rg -v '^tests/' \| wc -l` | 5,108 | VERIFIED |
| EV-M01-013 | `git remote get-url origin` | `github.com/yusufbayuw/foundationOS` | VERIFIED |
| EV-M01-014 | `ls Modules/ \| wc -l` | 45 | VERIFIED |
| EV-M01-015 | `find . -path ./vendor -prune -o -path ./node_modules -prune -o -path ./.git -prune -o -type f -print \| wc -l` | 6,071 | VERIFIED |

---

## 7. Validation Checklist

### Coverage

- [x] Repository name, branch, commit recorded
- [x] Total files counted (tracked + filesystem note)
- [x] Source / test / migration / config counts separated
- [x] Excluded directories documented
- [x] Scan assumptions explicit
- [x] No business analysis included

### Missing Evidence

- [ ] Untracked file inventory (`storage/app/` local extracts) — **deferred** to Stage 02 if needed
- [ ] `vendor/` package manifest enumeration — **out of scope** for Stage 01

### Ambiguous Findings

- [ ] **AF-M01-01:** Label “source files” = PHP excl. tests; blade/JS/CSS not included in source total (reported separately). **Confidence:** INFERRED convention.
- [ ] **AF-M01-02:** Composer `name` (`laravel/laravel`) ≠ product name (`FoundationOS`). Both recorded; no merge.

### Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| Untracked local files skew filesystem vs git count | Low | Prefer `git ls-files` for audit |
| Large `Modules/` tree (4,685 PHP) | Medium | Stage 02+ use module-scoped scans |
| `vendor/` excluded — runtime deps not manifest | Low | Documented in excluded dirs |

---

## 8. Stage Gate

| Criterion | Status |
|-----------|--------|
| Artifact `00-repository-manifest.md` produced | **Done** |
| Evidence IDs assigned | **Done** (EV-M01-001 … EV-M01-015) |
| Business analysis absent | **Confirmed** |
| Ready for REAP Stage 02 | **Yes** (conditional on checklist acceptance) |

---

## 9. Regenerate Counts

```bash
git rev-parse HEAD
git branch --show-current
git ls-files | wc -l
git ls-files '*.php' | wc -l
git ls-files '*.php' | rg -v '^tests/' | wc -l
git ls-files 'tests/**/*.php' | wc -l
git ls-files 'database/migrations/*.php' 'Modules/*/database/migrations/*.php' | wc -l
git ls-files 'config/*.php' 'Modules/*/config/*.php' | wc -l
ls Modules/ | wc -l
```

---

*REAP Stage 01 — Repository Manifest. No business logic. Evidence-first.*
