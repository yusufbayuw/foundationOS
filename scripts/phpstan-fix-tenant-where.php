#!/usr/bin/env php
<?php

$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$vars = ['query', 'inner', 'builder', 'q', 'q2'];
$updated = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();

    if (str_contains($path, '/vendor/') || str_contains($path, '/node_modules/')) {
        continue;
    }

    if (! str_starts_with($path, $root.'/app/') && ! str_starts_with($path, $root.'/Modules/')) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    $original = $content;

    foreach ($vars as $var) {
        $content = preg_replace(
            '/(\$'.preg_quote($var, '/').')->where\(\'tenant_id\',/',
            '$1->where($1->getModel()->qualifyColumn(\'tenant_id\'),',
            $content,
        ) ?? $content;
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
    }
}

echo "Updated {$updated} file(s).\n";
