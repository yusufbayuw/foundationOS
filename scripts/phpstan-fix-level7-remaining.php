#!/usr/bin/env php
<?php

/**
 * Fix common PHPStan level 7 patterns from a raw error file.
 *
 * Usage: vendor/bin/phpstan analyse --level=7 --error-format=raw 2>&1 | grep '^/' > /tmp/e.txt
 *        php scripts/phpstan-fix-level7-remaining.php /tmp/e.txt
 */
$errorsFile = $argv[1] ?? '/tmp/phpstan-l7.txt';
if (! is_file($errorsFile)) {
    fwrite(STDERR, "Missing errors file: {$errorsFile}\n");
    exit(1);
}

$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';

/** @var array<string, true> */
$propertyFiles = [];
/** @var array<string, list<string>> */
$methodReturnFiles = [];

foreach (file($errorsFile, FILE_IGNORE_NEW_LINES) as $line) {
    if (! str_starts_with($line, '/')) {
        continue;
    }

    if (str_contains($line, 'missingType.iterableValue') && str_contains($line, 'Property ')) {
        if (preg_match('#^(/[^:]+):(\d+):Property ([^:]+)::(\$\w+)#', $line, $m)) {
            $propertyFiles[$m[1].':'.$m[4]] = true;
        }
    }
}

foreach (array_keys($propertyFiles) as $key) {
    [$file, $prop] = explode(':', $key, 2);
    if (! is_file($file)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $propName = ltrim($prop, '$');
    $pattern = '/((?:public|protected|private) '.preg_quote($propName, '/').' = [^;]+;)/';

    if (preg_match($pattern, $content) && ! preg_match('/\/\*\*[^*]*@var[^*]*'.preg_quote($prop, '/').'[^*]*\*\//', $content)) {
        $replacement = "/** @var array<string, mixed> */\n    ".'$1';
        $newContent = preg_replace('/(\n    )'.preg_quote('$1', '/').'/', '', $pattern);
        $newContent = preg_replace($pattern, "/** @var array<string, mixed> */\n    \$1", $content, 1);
        if (is_string($newContent) && $newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Property typed: {$file} {$prop}\n";
        }
    }
}

// Fix resolveCoa / keywords params to accept list<string>
$keywordFiles = [
    'Modules/Inventory/app/Services/StockJournalService.php',
];
foreach ($keywordFiles as $rel) {
    $path = $root.'/'.$rel;
    if (! is_file($path)) {
        continue;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }
    $fixed = preg_replace(
        '/@param\s+array<string, mixed>\s+\$keywords/',
        '@param array<int, string> $keywords',
        $content,
    );
    if (is_string($fixed) && $fixed !== $content) {
        file_put_contents($path, $fixed);
        echo "Updated keywords param: {$rel}\n";
    }
}

echo "Done.\n";
