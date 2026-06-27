#!/usr/bin/env php
<?php

$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$vars = ['query', 'inner', 'builder', 'q', 'q2'];
$columns = [
    'organization_id',
    'academic_period_id',
    'province_id',
    'book_id',
    'class_id',
    'workflow_id',
    'workflow_step_id',
    'book_category_id',
    'is_super_admin',
    'is_posted',
    'entity_id',
    'invoice_number',
];
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

    foreach ($columns as $column) {
        foreach ($vars as $var) {
            $content = preg_replace(
                '/(\$'.preg_quote($var, '/').')->where\(\''.preg_quote($column, '/').'\',/',
                '$1->where($1->getModel()->qualifyColumn(\''.$column.'\'),',
                $content,
            ) ?? $content;

            $content = preg_replace(
                '/(\$'.preg_quote($var, '/').')->orWhere\(\''.preg_quote($column, '/').'\',/',
                '$1->orWhere($1->getModel()->qualifyColumn(\''.$column.'\'),',
                $content,
            ) ?? $content;
        }
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
    }
}

echo "Updated {$updated} file(s).\n";
