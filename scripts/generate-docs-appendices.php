<?php

declare(strict_types=1);

$auth = json_decode(file_get_contents(__DIR__.'/../docs/catalogs/authorization-matrix.json'), true, 512, JSON_THROW_ON_ERROR);
$api = json_decode(file_get_contents(__DIR__.'/../docs/catalogs/api-routes-catalog.json'), true, 512, JSON_THROW_ON_ERROR);

$methods = implode(', ', $auth['shield']['policy_methods']);
$separator = $auth['shield']['permission_separator'];
$exampleSubject = $auth['resources_by_module']['School']['resources'][0]['permission_subject'] ?? 'Student';
$examplePerms = array_slice($auth['resources_by_module']['School']['resources'][0]['permissions'] ?? [], 0, 3);

$authMd = <<<MD
# Authorization Matrix

Evidence-based Shield / Spatie permission catalog for FoundationOS admin panel (`/admin`).

**Generated:** {$auth['generated_at']}  
**Source:** `scripts/extract-authorization-matrix.php` → `docs/catalogs/authorization-matrix.json`  
**Full machine catalog:** `docs/catalogs/authorization-matrix.json` ({$auth['resource_count']} resources, ~{$auth['permission_count_estimate']} permissions)

---

## Permission naming (Filament Shield)

| Setting | Value | Evidence |
|---------|-------|----------|
| Separator | `{$separator}` | `config/filament-shield.php` `permissions.separator` |
| Case | `{$auth['shield']['permission_case']}` | `config/filament-shield.php` `permissions.case` |
| Policy methods | {$methods} | `config/filament-shield.php` `policies.methods` |
| Teams mode | `{$auth['shield']['team_foreign_key']}` as team key | `config/permission.php` |

**Pattern:** `{Method}{$separator}{ModelShortName}` — example for `{$exampleSubject}`:

MD;

foreach ($examplePerms as $perm) {
    $authMd .= "- `{$perm}`\n";
}

$authMd .= <<<MD

Each of **{$auth['resource_count']}** `ModuleResource` classes maps to **12** resource permissions + **16** custom exam permissions in `config/filament-shield.php`.

---

## Panel access (authentication boundary)

| Panel | Gate | Rule | Evidence |
|-------|------|------|----------|
| `admin` | `User::canAccessPanel('admin')` | `is_super_admin` OR `user_tenant_roles` exists | `User.php:471-476` |
| `platform` | `User::canAccessPanel('platform')` | Spatie role `platform_owner` (team `tenant_id = 0`) | `User.php:465-468` |
| `parent` | `User::canAccessPanel('parent')` | `parent_students.parent_user_id` link | `User.php:479-481` |

---

## Global authorization bypass

| Mechanism | Behavior | Evidence |
|-----------|----------|----------|
| `users.is_super_admin` | `Gate::before` returns `true` for all abilities | `AppServiceProvider.php:93-98` |
| Shield `super_admin` role | Configured; teams-scoped per tenant via `shield:super-admin` | `config/filament-shield.php`, `CLAUDE.md` |
| `panel_user` role | Basic panel access role (Shield) | `config/filament-shield.php` |

---

## Domain membership vs panel permissions

| Layer | Purpose | Tables / classes |
|-------|---------|------------------|
| Domain membership | Which tenant/org a user belongs to | `user_tenant_roles`, `tenant_roles` |
| Panel authorization | What actions are allowed in Filament | Spatie `roles`, `permissions`, Shield-generated keys |
| API authorization | Token + tenant resolve; **not** Shield per route | Sanctum + `resolve.api.tenant` |

---

## Custom permissions (Exam module)

| Permission key | Label |
|----------------|-------|

MD;

foreach ($auth['custom_permissions'] as $custom) {
    $authMd .= "| `{$custom['name']}` | {$custom['label']} |\n";
}

$authMd .= <<<MD

Evidence: `config/filament-shield.php` `custom_permissions`.

---

## Resources by module

| Module | Resources | Example permission subject |
|--------|-----------|----------------------------|

MD;

$modules = $auth['resources_by_module'];
ksort($modules);

foreach ($modules as $module => $data) {
    $first = $data['resources'][0]['permission_subject'] ?? '—';
    $authMd .= "| {$module} | {$data['resource_count']} | `{$first}` |\n";
}

$authMd .= <<<MD

---

## Sample resource permissions (School module)

| Resource class | Model | Permissions |
|----------------|-------|-------------|

MD;

foreach (array_slice($auth['resources_by_module']['School']['resources'] ?? [], 0, 5) as $resource) {
    $perms = implode('`, `', array_slice($resource['permissions'], 0, 4)).'` …';
    $authMd .= "| `{$resource['resource_class']}` | `{$resource['model_class']}` | `{$perms}` |\n";
}

$authMd .= <<<MD

---

## Regenerate

```bash
php scripts/extract-authorization-matrix.php
php scripts/generate-docs-appendices.php
```

MD;

file_put_contents(__DIR__.'/../AUTHORIZATION_MATRIX.md', $authMd);

$apiMd = <<<MD
# API Routes Catalog

Complete application API route list (`php artisan route:list` bootstrap, URI prefix `api/`).

**Generated:** {$api['generated_at']}  
**Source:** `scripts/extract-api-routes.php` → `docs/catalogs/api-routes-catalog.json`  
**Route count:** {$api['route_count']}

---

## Summary by module

| Module | Routes |
|--------|--------|

MD;

$byModule = $api['routes_by_module'];
uksort($byModule, static fn (string $a, string $b): int => strcmp($a, $b));

foreach ($byModule as $module => $routes) {
    $apiMd .= '| '.$module.' | '.count($routes)." |\n";
}

$apiMd .= <<<MD

---

## Middleware patterns

| Pattern | Routes | Meaning |
|---------|--------|---------|
| `auth:sanctum` + `resolve.api.tenant` | Core mobile/integrator v1/v2 | Bearer token with tenant context |
| `auth:sanctum` only | `api/exam/runtime/*` | Exam runtime (no `resolve.api.tenant` on route group) |
| `throttle:api` | Most v1/v2 groups | 60 req/min per token or IP (`routes/api.php`) |
| `idempotency` | POST applicants, payments, leave-requests | Duplicate-safe writes |
| Public (no auth) | `openapi.json`, `letters/verify`, `api/inquiry` | See table below |

---

## Full route table

| Method | URI | Route name | Module | Middleware | Action |
|--------|-----|------------|--------|------------|--------|

MD;

foreach ($api['routes'] as $route) {
    $methods = implode('|', array_filter($route['methods'], static fn (string $m): bool => $m !== 'HEAD'));
    if ($methods === '') {
        $methods = 'GET';
    }
    $middleware = implode(', ', $route['middleware']);
    $name = $route['name'] ?? '—';
    $action = str_replace('\\', '\\', $route['action']);
    $apiMd .= "| {$methods} | `{$route['uri']}` | {$name} | {$route['module']} | {$middleware} | `{$action}` |\n";
}

$apiMd .= <<<MD

---

## Regenerate

```bash
php scripts/extract-api-routes.php
php scripts/generate-docs-appendices.php
```

MD;

file_put_contents(__DIR__.'/../API_ROUTES.md', $apiMd);

echo "Wrote AUTHORIZATION_MATRIX.md and API_ROUTES.md\n";
