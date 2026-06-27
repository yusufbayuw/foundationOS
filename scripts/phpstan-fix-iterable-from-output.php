#!/usr/bin/env php
<?php

/**
 * Add generic array PHPDocs for PHPStan level 7 missingType.iterableValue errors.
 *
 * Usage: vendor/bin/phpstan analyse --level=7 --error-format=raw 2>&1 | php scripts/phpstan-fix-iterable-from-output.php
 */
$input = stream_get_contents(STDIN);
if ($input === false || trim($input) === '') {
    fwrite(STDERR, "Pipe PHPStan raw output into this script.\n");
    exit(1);
}

/** @var array<string, list<string>> */
$targets = [];

foreach (explode("\n", $input) as $line) {
    if (! str_starts_with($line, '/')) {
        continue;
    }

    if (! str_contains($line, 'missingType.iterableValue')) {
        continue;
    }

    if (! preg_match('#^(/[^:]+):(\d+):Method ([^:]+)::([^(]+)\(\)(?: return type has no value type specified in iterable type array| has parameter \$([^\s]+) with no value type specified in iterable type array)#', $line, $m)) {
        continue;
    }

    $file = $m[1];
    $method = $m[4];
    $param = $m[5] ?? null;
    $key = $param === null ? '@return' : '@param:'.$param;
    $targets[$file][$method][$key] = true;
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

    foreach ($methods as $method => $docs) {
        if (isset($docs['@return'])) {
            $content = addReturnArrayDoc($content, $method);
        }

        foreach ($docs as $key => $_) {
            if (! str_starts_with($key, '@param:')) {
                continue;
            }

            $param = substr($key, 7);
            $content = addParamArrayDoc($content, $method, $param);
        }
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        $updated++;
        echo "Updated: {$file}\n";
    }
}

echo "Updated {$updated} file(s).\n";

function addReturnArrayDoc(string $content, string $method): string
{
    $pattern = '/(?:(    \/\*\*(?:(?!    \*\/)[\s\S])*?    \*\/\s*\n)+)?(    (?:public|protected|private) (?:static )?function '.preg_quote($method, '/').'\([^)]*\): array)/';

    return preg_replace_callback($pattern, function (array $matches): string {
        $existing = $matches[1] ?? '';

        if ($existing !== '' && preg_match('/@return\s+array<[^>]+>/', $existing)) {
            return $matches[0];
        }

        if ($existing !== '') {
            if (! preg_match('/@return\s+array<[^>]+>/', $existing)) {
                $existing = preg_replace('/(\s+\*\/\s*\n)$/', "     * @return array<string, mixed>\n$1", $existing) ?? $existing;
            }

            return $existing.$matches[2];
        }

        return "    /**\n     * @return array<string, mixed>\n     */\n".$matches[2];
    }, $content, 1) ?? $content;
}

function addParamArrayDoc(string $content, string $method, string $param): string
{
    $pattern = '/(?:(    \/\*\*(?:(?!    \*\/)[\s\S])*?    \*\/\s*\n)+)?(    (?:public|protected|private) (?:static )?function '.preg_quote($method, '/').'\(([^)]*)\))/';

    return preg_replace_callback($pattern, function (array $matches) use ($param): string {
        if (! preg_match('/\barray\s+\$'.preg_quote($param, '/').'\b/', $matches[3])) {
            return $matches[0];
        }

        $existing = $matches[1] ?? '';
        $needle = '@param array<string, mixed> $'.$param;

        if ($existing !== '' && str_contains($existing, $needle)) {
            return $matches[0];
        }

        if ($existing !== '' && preg_match('/@param\s+array<[^>]+>\s+\$'.preg_quote($param, '/').'\b/', $existing)) {
            return $matches[0];
        }

        if ($existing !== '') {
            $existing = preg_replace('/(\s+\*\/\s*\n)$/', "     * {$needle}\n$1", $existing) ?? $existing;

            return $existing.$matches[2];
        }

        return "    /**\n     * {$needle}\n     */\n".$matches[2];
    }, $content, 1) ?? $content;
}
