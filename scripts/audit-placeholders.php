<?php

/**
 * Sprint 4 — Epic 5.1: Audit placeholders and helperText across Filament resources.
 *
 * Categorises each occurrence as:
 *   decorative  — '-', '', '0', single symbol → no action needed
 *   informative — narrative text (English or Indonesian) → candidate for FilamentUi::text()
 *   dynamic     — closure/variable → review case-by-case
 *   wrapped     — already uses FilamentUi → compliant
 *
 * Usage:
 *   php scripts/audit-placeholders.php [path ...]
 *
 * Output: JSON to stdout (pipe to a file if needed).
 */
$paths = array_slice($argv, 1);
if (empty($paths)) {
    $paths = [
        __DIR__.'/../Modules',
        __DIR__.'/../app/Filament',
    ];
}

$results = [];

function findPhpFiles(string $path): Generator
{
    if (is_file($path) && str_ends_with($path, '.php')) {
        yield $path;

        return;
    }
    if (! is_dir($path)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') {
            yield $f->getPathname();
        }
    }
}

function classify(string $line): string
{
    if (str_contains($line, 'FilamentUi::')) {
        return 'wrapped';
    }
    if (str_contains($line, 'fn (') || str_contains($line, 'function (') || str_contains($line, '$')) {
        return 'dynamic';
    }
    if (preg_match("/->(?:placeholder|helperText)\s*\(\s*['\"]([-0]?)['\"]/", $line)) {
        return 'decorative';
    }

    return 'informative';
}

foreach ($paths as $scanPath) {
    foreach (findPhpFiles($scanPath) as $file) {
        $lines = file($file);
        foreach ($lines as $i => $line) {
            if (str_contains($line, 'fos:lint-ignore')) {
                continue;
            }
            if (! preg_match('/->(?:placeholder|helperText)\s*\(/u', $line)) {
                continue;
            }
            preg_match('/->(?:placeholder|helperText)/', $line, $typeMatch);
            $type = $typeMatch[0] ?? '->?';

            $category = classify($line);
            $results[] = [
                'file' => str_replace(dirname(__DIR__).'/', '', $file),
                'line' => $i + 1,
                'category' => $category,
                'kind' => ltrim($type, '->'),
                'snippet' => trim($line),
            ];
        }
    }
}

$summary = array_count_values(array_column($results, 'category'));
$output = [
    'generated_at' => date('Y-m-d H:i:s'),
    'summary' => $summary,
    'total' => count($results),
    'items' => $results,
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n";
