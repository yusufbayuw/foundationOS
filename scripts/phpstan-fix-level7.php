#!/usr/bin/env php
<?php

/**
 * Bulk fixes for PHPStan level 7 missing return/parameter types.
 * Run: php scripts/phpstan-fix-level7.php [--dry-run]
 */
$dryRun = in_array('--dry-run', $argv, true);
$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';
$updated = 0;

$patterns = [
    $root.'/Modules/*/app/Http/Controllers/*PdfController.php',
    $root.'/Modules/*/app/Http/Controllers/*Controller.php',
    $root.'/Modules/*/app/Filament/Resources/**/*Resource.php',
];

$files = [];
foreach ($patterns as $pattern) {
    $files = array_merge($files, glob($pattern) ?: []);
}

$files = array_values(array_unique($files));
sort($files);

foreach ($files as $file) {
    $original = file_get_contents($file);
    if ($original === false) {
        continue;
    }

    $content = $original;

    if (str_contains($file, 'PdfController.php') || usesRendersTenantPdf($content)) {
        $content = fixPdfController($content);
    }

    if (isModuleScaffoldController($content)) {
        $content = fixModuleScaffoldController($content);
    }

    if (str_contains($content, 'function globalSearchAttributes()')) {
        $content = fixGlobalSearchAttributes($content);
    }

    if (str_contains($content, 'function globalSearchResultDetails(')) {
        $content = fixGlobalSearchResultDetails($content);
    }

    if ($content !== $original) {
        if (! $dryRun) {
            file_put_contents($file, $content);
        }
        $updated++;
        echo ($dryRun ? '[dry-run] ' : '')."Updated: {$file}\n";
    }
}

echo ($dryRun ? '[dry-run] ' : '')."Updated {$updated} file(s).\n";

function usesRendersTenantPdf(string $content): bool
{
    return str_contains($content, 'RendersTenantPdf');
}

function isModuleScaffoldController(string $content): bool
{
    return str_contains($content, 'moduleView(')
        && str_contains($content, 'public function index()')
        && str_contains($content, 'public function destroy($id)');
}

function ensureUse(string $content, string $import, string $fqcn): string
{
    if (str_contains($content, "use {$fqcn};")) {
        return $content;
    }

    if (preg_match('/^namespace [^;]+;\R/m', $content, $match, PREG_OFFSET_CAPTURE)) {
        $insertAt = $match[0][1] + strlen($match[0][0]);

        return substr($content, 0, $insertAt)."use {$fqcn};\n".substr($content, $insertAt);
    }

    return $content;
}

function fixPdfController(string $content): string
{
    $content = ensureUse($content, 'Response', 'Symfony\Component\HttpFoundation\Response');

    $content = preg_replace(
        '/public function __invoke\(([^)]*)\)(\s*\{)/',
        'public function __invoke($1): Response$2',
        $content,
    ) ?? $content;

    return $content;
}

function fixModuleScaffoldController(string $content): string
{
    $content = ensureUse($content, 'View', 'Illuminate\Contracts\View\View');

    $replacements = [
        '/public function index\(\)(\s*\{)/' => 'public function index(): View$1',
        '/public function create\(\)(\s*\{)/' => 'public function create(): View$1',
        '/public function store\(Request \$request\)(\s*\{)/' => 'public function store(Request $request): void$1',
        '/public function show\(\$id\)(\s*\{)/' => 'public function show(int|string $id): View$1',
        '/public function edit\(\$id\)(\s*\{)/' => 'public function edit(int|string $id): View$1',
        '/public function update\(Request \$request, \$id\)(\s*\{)/' => 'public function update(Request $request, int|string $id): void$1',
        '/public function destroy\(\$id\)(\s*\{)/' => 'public function destroy(int|string $id): void$1',
    ];

    foreach ($replacements as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content) ?? $content;
    }

    return $content;
}

function fixGlobalSearchAttributes(string $content): string
{
    if (preg_match('/\/\*\*\s*\R\s*\*\s*@return array<int, string>\s*\R\s*\*\/\s*\R\s*protected static function globalSearchAttributes\(\)/', $content)) {
        return $content;
    }

    return preg_replace(
        '/(\n\s*)protected static function globalSearchAttributes\(\): array/',
        "$1/**\n$1 * @return array<int, string>\n$1 */\n$1protected static function globalSearchAttributes(): array",
        $content,
        1,
    ) ?? $content;
}

function fixGlobalSearchResultDetails(string $content): string
{
    if (preg_match('/\/\*\*\s*\R\s*\*\s*@return array<string, string>\s*\R\s*\*\/\s*\R\s*protected static function globalSearchResultDetails\(/', $content)) {
        return $content;
    }

    return preg_replace(
        '/(\n\s*)protected static function globalSearchResultDetails\(Model \$record\): array/',
        "$1/**\n$1 * @return array<string, string>\n$1 */\n$1protected static function globalSearchResultDetails(Model \$record): array",
        $content,
        1,
    ) ?? $content;
}
