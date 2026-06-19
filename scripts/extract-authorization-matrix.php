<?php

declare(strict_types=1);
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$shieldConfig = require __DIR__.'/../config/filament-shield.php';
$permissionConfig = require __DIR__.'/../config/permission.php';

$separator = $shieldConfig['permissions']['separator'] ?? ':';
$case = $shieldConfig['permissions']['case'] ?? 'pascal';
$policyMethods = $shieldConfig['policies']['methods'] ?? [
    'viewAny', 'view', 'create', 'update', 'delete', 'deleteAny', 'restore',
    'forceDelete', 'forceDeleteAny', 'restoreAny', 'replicate', 'reorder',
];

$toPermissionCase = static function (string $value) use ($case): string {
    return match ($case) {
        'snake' => str($value)->snake()->toString(),
        'kebab' => str($value)->kebab()->toString(),
        'pascal' => str($value)->studly()->toString(),
        'camel' => str($value)->camel()->toString(),
        'upper_snake' => strtoupper(str($value)->snake()->toString()),
        'lower_snake' => str($value)->snake()->toString(),
        default => str($value)->studly()->toString(),
    };
};

$resourceFiles = [];
$searchRoots = [
    __DIR__.'/../Modules',
    __DIR__.'/../app/Filament/Resources',
];

foreach ($searchRoots as $root) {
    if (! is_dir($root)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        $file = $fileInfo->getPathname();
        if (! str_ends_with($file, 'Resource.php')) {
            continue;
        }
        if (str_contains($file, '/Schemas/')
            || str_contains($file, '/Tables/')
            || str_contains($file, '/RelationManagers/')) {
            continue;
        }
        $resourceFiles[] = $file;
    }
}

$resourceFiles = array_values(array_unique($resourceFiles));

$resources = [];

foreach ($resourceFiles as $file) {
    $content = file_get_contents($file);
    if ($content === false || ! preg_match('/extends\s+ModuleResource/', $content)) {
        continue;
    }

    if (! preg_match('/namespace\s+([^;]+);/', $content, $nsMatch)) {
        continue;
    }

    if (! preg_match('/class\s+(\w+Resource)\b/', $content, $classMatch)) {
        continue;
    }

    $fqcn = $nsMatch[1].'\\'.$classMatch[1];
    $module = 'app';
    if (preg_match('#Modules/([^/]+)/#', $file, $moduleMatch)) {
        $module = $moduleMatch[1];
    }

    $modelClass = null;
    if (preg_match('/protected\s+static\s+\?string\s+\$model\s*=\s*([^;]+);/', $content, $modelMatch)) {
        $modelExpr = trim($modelMatch[1]);
        if (preg_match('/::class$/', $modelExpr)) {
            $modelClass = str_replace('::class', '', $modelExpr);
            if (! str_contains($modelClass, '\\')) {
                $modelClass = $nsMatch[1].'\\'.ltrim($modelClass, '\\');
            }
        }
    }

    $modelShort = $modelClass ? class_basename($modelClass) : class_basename(str_replace('Resource', '', $classMatch[1]));
    $subject = $toPermissionCase($modelShort);

    $permissions = [];
    foreach ($policyMethods as $method) {
        $permissions[] = $toPermissionCase($method).$separator.$subject;
    }

    $resources[] = [
        'module' => $module,
        'resource_class' => $fqcn,
        'model_class' => $modelClass,
        'permission_subject' => $subject,
        'permissions' => $permissions,
        'file' => str_replace(__DIR__.'/../', '', $file),
    ];
}

usort($resources, static fn (array $a, array $b): int => [$a['module'], $a['resource_class']] <=> [$b['module'], $b['resource_class']]);

$byModule = [];
foreach ($resources as $resource) {
    $byModule[$resource['module']] ??= [];
    $byModule[$resource['module']][] = $resource;
}

$customPermissions = [];
foreach ($shieldConfig['custom_permissions'] ?? [] as $key => $label) {
    $customPermissions[] = [
        'name' => (string) $key,
        'label' => (string) $label,
    ];
}

$output = [
    'generated_at' => gmdate('c'),
    'source' => 'scripts/extract-authorization-matrix.php',
    'shield' => [
        'permission_separator' => $separator,
        'permission_case' => $case,
        'policy_methods' => $policyMethods,
        'super_admin_role' => $shieldConfig['super_admin']['name'] ?? 'super_admin',
        'panel_user_role' => $shieldConfig['panel_user']['name'] ?? 'panel_user',
        'tenant_model' => $shieldConfig['tenant_model'] ?? null,
        'teams_enabled' => (bool) ($permissionConfig['teams'] ?? false),
        'team_foreign_key' => $permissionConfig['team_foreign_key'] ?? 'tenant_id',
    ],
    'panel_access' => [
        'admin' => [
            'gate' => 'User::canAccessPanel(admin)',
            'rules' => [
                'is_super_admin OR user_tenant_roles exists',
            ],
            'evidence' => 'Modules/Core/app/Models/User.php:471-476',
        ],
        'platform' => [
            'gate' => 'User::canAccessPanel(platform)',
            'rules' => [
                'Spatie role platform_owner with team tenant_id=0',
            ],
            'evidence' => 'Modules/Core/app/Models/User.php:465-468',
        ],
        'parent' => [
            'gate' => 'User::canAccessPanel(parent)',
            'rules' => [
                'parent_students.parent_user_id link exists',
            ],
            'evidence' => 'Modules/Core/app/Models/User.php:479-481',
        ],
    ],
    'global_bypass' => [
        'users.is_super_admin' => 'Gate::before returns true for all abilities',
        'evidence' => 'app/Providers/AppServiceProvider.php:93-98',
    ],
    'resource_count' => count($resources),
    'permission_count_estimate' => count($resources) * count($policyMethods) + count($customPermissions),
    'custom_permissions' => $customPermissions,
    'resources_by_module' => array_map(
        static fn (array $items): array => [
            'resource_count' => count($items),
            'resources' => $items,
        ],
        $byModule
    ),
];

$jsonPath = __DIR__.'/../docs/catalogs/authorization-matrix.json';
file_put_contents($jsonPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

echo "Wrote {$jsonPath}\n";
echo 'Resources: '.count($resources)."\n";
echo 'Estimated permissions: '.$output['permission_count_estimate']."\n";
