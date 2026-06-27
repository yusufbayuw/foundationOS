#!/usr/bin/env php
<?php

/**
 * Fix mangled PHPDoc lines produced by automated iterable fixers.
 */
$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';
$paths = array_merge(
    glob($root.'/app/**/*.php') ?: [],
    glob($root.'/Modules/*/app/**/*.php') ?: [],
    glob($root.'/Modules/*/tests/**/*.php') ?: [],
);

$updated = 0;

foreach ($paths as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $fixed = preg_replace(
        '/(\* @param array<string, mixed> \$\w+)\s+\* @param array<string, mixed> (\$\w+)\s*\n\s*\n\s*\*\//',
        "$1\n     * @param array<string, mixed> $2\n     */",
        $content,
    ) ?? $content;

    $fixed = preg_replace(
        '/(\* @param array<string, mixed> \$\w+)\s+\* @return array<string, mixed>\s*\n\s*\n\s*\*\//',
        "$1\n     */",
        $fixed,
    ) ?? $fixed;

    if ($fixed !== $content) {
        file_put_contents($file, $fixed);
        $updated++;
    }
}

echo "Fixed {$updated} file(s).\n";
