#!/usr/bin/env php
<?php

/**
 * Align factory definition() PHPDoc with Laravel Factory generics.
 */
$files = array_merge(
    glob(__DIR__.'/../database/factories/*Factory.php') ?: [],
    glob(__DIR__.'/../Modules/*/database/factories/*Factory.php') ?: [],
);

$updated = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    if (! preg_match('/@extends\s+Factory<([\w\\\\]+)>/', $content, $modelMatch)) {
        continue;
    }

    $model = ltrim($modelMatch[1], '\\');
    $modelShort = str_contains($model, '\\') ? substr($model, strrpos($model, '\\') + 1) : $model;
    $replacement = "     * @return array<model property of {$modelShort}, mixed>";

    $newContent = preg_replace(
        '/\*\s*@return array<string, mixed>/',
        $replacement,
        $content,
        1,
    );

    if (is_string($newContent) && $newContent !== $content) {
        file_put_contents($file, $newContent);
        $updated++;
    }
}

echo "Updated {$updated} factory file(s).\n";
