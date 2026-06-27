#!/usr/bin/env php
<?php

/**
 * Fix incorrect @return docs using PHPStan return.type errors.
 */
$input = stream_get_contents(STDIN);
if ($input === false || trim($input) === '') {
    fwrite(STDERR, "Pipe PHPStan raw output into this script.\n");
    exit(1);
}

/** @var array<string, array<string, string>> */
$targets = [];

foreach (explode("\n", $input) as $line) {
    if (! str_contains($line, 'return.type') || ! str_contains($line, ' should return ')) {
        continue;
    }

    if (! preg_match('#^(/[^:]+):(\d+):Method ([^:]+)::([^(]+)\(\) should return (.+?) but returns .+#', $line, $m)) {
        continue;
    }

    $targets[$m[1]][$m[4]] = $m[5];
}

$updated = 0;

foreach ($targets as $file => $methods) {
    if (! is_file($file)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $original = $content;

    foreach ($methods as $method => $expectedReturn) {
        $content = replaceMethodReturnDoc($content, $method, $expectedReturn);
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        $updated++;
        echo "Updated: {$file}\n";
    }
}

echo "Updated {$updated} file(s).\n";

function replaceMethodReturnDoc(string $content, string $method, string $expectedReturn): string
{
    $pattern = '/(\/\*\*(?:(?!\*\/)[\s\S])*?\*\/\s*\n\s*(?:public|protected|private)(?: static)? function '.preg_quote($method, '/').'\([^)]*\)(?:\s*:\s*[^\n{]+)?)/';

    return preg_replace_callback($pattern, function (array $matches) use ($expectedReturn): string {
        $block = $matches[1];

        if (preg_match('/\/\*\*(?:(?!\*\/)[\s\S])*?\*\//', $block, $docMatch)) {
            $doc = $docMatch[0];

            if (preg_match('/@return\s+[^\n]+/', $doc)) {
                $doc = preg_replace('/@return\s+[^\n]+/', '@return '.$expectedReturn, $doc) ?? $doc;
            } else {
                $doc = preg_replace('/(\s+\*\/)/', "     * @return {$expectedReturn}\n$1", $doc) ?? $doc;
            }

            return str_replace($docMatch[0], $doc, $block);
        }

        return "    /**\n     * @return {$expectedReturn}\n     */\n".$block;
    }, $content, 1) ?? $content;
}
