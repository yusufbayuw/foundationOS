<?php

/**
 * Detects tenant_id Select fields in Filament forms that should use TenantField::make().
 *
 * Usage:
 *   php scripts/lint-tenant-fields.php [path ...]
 *
 * Exit code: 0 — no violations, 1 — violations found.
 *
 * Suppress per line: // fos:lint-ignore-tenant-field
 */
$paths = array_slice($argv, 1);

if ($paths === []) {
    $paths = [
        __DIR__.'/../Modules/Core/app/Filament',
        __DIR__.'/../Modules/Enrollment/app/Filament',
        __DIR__.'/../Modules/Finance/app/Filament',
        __DIR__.'/../Modules/School/app/Filament',
        __DIR__.'/../Modules/Procurement/app/Filament',
        __DIR__.'/../Modules/Workflow/app/Filament',
    ];
}

$pattern = "/Select::make\(\s*['\"]tenant_id['\"]\s*\)/";
$violations = [];

foreach ($paths as $scanPath) {
    if (! is_dir($scanPath) && ! is_file($scanPath)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($scanPath, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $lines = file($file->getPathname(), FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            continue;
        }

        foreach ($lines as $lineNumber => $lineContent) {
            if (str_contains($lineContent, 'fos:lint-ignore-tenant-field')) {
                continue;
            }

            if (! preg_match($pattern, $lineContent)) {
                continue;
            }

            if (str_contains($lineContent, 'TenantField::')) {
                continue;
            }

            $violations[] = [
                'file' => $file->getPathname(),
                'line' => $lineNumber + 1,
                'content' => trim($lineContent),
            ];
        }
    }
}

if ($violations === []) {
    echo "✅ Tenant field lint passed — no Select::make('tenant_id') in scanned paths.\n";
    exit(0);
}

echo '❌ Tenant field lint FAILED — '.count($violations)." violation(s):\n\n";

foreach ($violations as $violation) {
    $relative = str_replace(dirname(__DIR__).'/', '', $violation['file']);
    echo "  {$relative}:{$violation['line']}\n";
    echo "  > {$violation['content']}\n\n";
}

echo "Fix: replace with TenantField::make() in tenant-scoped panel resources.\n";

exit(1);
