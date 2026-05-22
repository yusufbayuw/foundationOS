<?php

/**
 * FoundationOS Translation Linter — Fase 1.3 (ROADMAPv2.md)
 *
 * Detects anti-patterns where labels, section titles, placeholders, or
 * helper text are hardcoded in English/Indonesian instead of going through
 * FilamentUi helpers.
 *
 * Usage:
 *   php scripts/lint-translations.php [path ...]
 *
 * Exit code:
 *   0 — no violations
 *   1 — one or more violations found
 *
 * Per-line suppression: add  // fos:lint-ignore-translation  at end of line.
 */
$paths = array_slice($argv, 1);
if (empty($paths)) {
    $paths = [
        __DIR__.'/../Modules',
        __DIR__.'/../app/Filament',
    ];
}

$violations = [];

/**
 * Rules: [description, regex, allowlist_regex|null]
 *
 * @var array<array{string, string, string|null}>
 */
$rules = [
    [
        'Field ->label() with hardcoded English string',
        '/->label\(\s*[\'"]([A-Z][a-zA-Z ]{2,})[\'"]/',
        null,
    ],
    [
        'Section::make() with hardcoded English title',
        '/Section::make\(\s*[\'"]([A-Z][a-zA-Z ]{2,})[\'"]/',
        null,
    ],
    [
        'Tabs\\Tab::make() with hardcoded English title',
        '/Tabs\\\\Tab::make\(\s*[\'"]([A-Z][a-zA-Z ]{2,})[\'"]/',
        null,
    ],
    [
        'Fieldset::make() with hardcoded English title',
        '/Fieldset::make\(\s*[\'"]([A-Z][a-zA-Z ]{2,})[\'"]/',
        null,
    ],
    [
        'Wizard\\Step::make() with hardcoded title',
        '/Wizard\\\\Step::make\(\s*[\'"]([A-Z][a-zA-Z ]{2,})[\'"]/',
        null,
    ],
    [
        '->placeholder() with hardcoded narrative text',
        '/->placeholder\(\s*[\'"]([A-Za-z ]{5,})[\'"]/',
        // Allow: '-', '0', single words that are clearly decorative
        '/^[\-0]$/',
    ],
    [
        '->helperText() with hardcoded string (use FilamentUi::text())',
        '/->helperText\(\s*[\'"][A-Za-z\x{00C0}-\x{024F}][^\'"]{4,}[\'"]\)/u',
        null,
    ],
];

/** Recursively find PHP files in the given directories */
function findPhpFiles(string $path): Generator
{
    if (is_file($path) && str_ends_with($path, '.php')) {
        yield $path;

        return;
    }

    if (! is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            yield $file->getPathname();
        }
    }
}

foreach ($paths as $scanPath) {
    foreach (findPhpFiles($scanPath) as $filePath) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            continue;
        }

        foreach ($lines as $lineNumber => $lineContent) {
            // Per-line suppression
            if (str_contains($lineContent, 'fos:lint-ignore-translation')) {
                continue;
            }

            foreach ($rules as [$description, $pattern, $allowlistPattern]) {
                if (! preg_match($pattern, $lineContent, $matches)) {
                    continue;
                }

                $captured = $matches[1] ?? '';

                // Skip if the captured value matches the allowlist
                if ($allowlistPattern !== null && preg_match($allowlistPattern, $captured)) {
                    continue;
                }

                // Skip if the value is already wrapped in FilamentUi
                if (str_contains($lineContent, 'FilamentUi::')) {
                    continue;
                }

                // Skip if passing a variable or function call
                if (str_contains($lineContent, '$') || str_contains($lineContent, 'fn ') || str_contains($lineContent, '__')) {
                    continue;
                }

                $violations[] = [
                    'file' => $filePath,
                    'line' => $lineNumber + 1,
                    'rule' => $description,
                    'content' => trim($lineContent),
                ];
            }
        }
    }
}

if (empty($violations)) {
    echo "✅ Translation lint passed — no violations found.\n";
    exit(0);
}

$count = count($violations);
echo "❌ Translation lint FAILED — {$count} violation(s) found:\n\n";

foreach ($violations as $v) {
    $relative = str_replace(dirname(__DIR__).'/', '', $v['file']);
    echo "  [{$v['rule']}]\n";
    echo "  {$relative}:{$v['line']}\n";
    echo "  > {$v['content']}\n\n";
}

echo "Fix: replace hardcoded strings with FilamentUi::field(...) or FilamentUi::text(...)\n";
echo "Suppress: add  // fos:lint-ignore-translation  at end of the line.\n";

exit(1);
