#!/usr/bin/env php
<?php

/**
 * Add @return generics to Eloquent relation methods missing PHPStan annotations.
 * Handles multiline relation bodies (withPivot chains, etc.).
 */
$dryRun = in_array('--dry-run', $argv, true);
$paths = array_slice($argv, 1);
$paths = array_values(array_filter($paths, fn (string $arg): bool => $arg !== '--dry-run'));

if ($paths === []) {
    $paths = array_merge(
        glob(__DIR__.'/../app/Models/*.php') ?: [],
        glob(__DIR__.'/../Modules/*/app/Models/*.php') ?: [],
    );
}

$relationTypes = [
    'BelongsTo' => 'belongsTo',
    'HasMany' => 'hasMany',
    'HasOne' => 'hasOne',
    'BelongsToMany' => 'belongsToMany',
    'MorphMany' => 'morphMany',
    'MorphOne' => 'morphOne',
    'MorphTo' => 'morphTo',
    'HasManyThrough' => 'hasManyThrough',
];

$updated = 0;

foreach ($paths as $file) {
    $original = file_get_contents($file);
    if ($original === false) {
        continue;
    }

    $content = annotateRelations($original, $relationTypes);

    if ($content !== $original) {
        if (! $dryRun) {
            file_put_contents($file, $content);
        }
        $updated++;
        echo ($dryRun ? '[dry-run] ' : '')."Updated: {$file}\n";
    }
}

echo ($dryRun ? '[dry-run] ' : '')."Updated {$updated} file(s).\n";

/**
 * @param  array<string, string>  $relationTypes
 */
function annotateRelations(string $content, array $relationTypes): string
{
    $uses = parseUseMap($content);
    $namespace = parseNamespace($content);
    $className = parseClassName($content);

    foreach ($relationTypes as $relationClass => $eloquentMethod) {
        $pattern = '/(?:(    \/\*\*(?:(?!    \*\/)[\s\S])*?    \*\/\s*\n)+)?(    public function (\w+)\(\): '.$relationClass.'\s*\n    \{[\s\S]*?return \$this->'.$eloquentMethod.'\([\s\S]*?\);\s*\n    \})/';

        $content = preg_replace_callback($pattern, function (array $matches) use ($relationClass, $eloquentMethod, $uses, $namespace, $className): string {
            $docblocks = $matches[1] ?? '';
            $methodBlock = $matches[2];

            if (preg_match('/@return\s+'.$relationClass.'</', $docblocks)) {
                return $matches[0];
            }

            if (! preg_match('/return \$this->'.$eloquentMethod.'\(([\s\S]*?)\);/', $methodBlock, $argMatch)) {
                return $matches[0];
            }

            $related = resolveRelationArgument(trim($argMatch[1]), $eloquentMethod, $uses, $namespace, $className);
            if ($related === null) {
                return $matches[0];
            }

            $doc = "    /**\n     * @return {$relationClass}<{$related}, \$this>\n     */\n";

            return $doc.$methodBlock;
        }, $content) ?? $content;
    }

    return dedupeRelationGenerics($content, array_keys($relationTypes));
}

/**
 * @param  list<string>  $relationClasses
 */
function dedupeRelationGenerics(string $content, array $relationClasses): string
{
    $pattern = implode('|', $relationClasses);

    return preg_replace_callback(
        '/((?:    \/\*\*\s*\n(?:     \*[^\n]*\n)+     \*\/\s*\n)+)(    public function \w+\(\): (?:'.$pattern.')\s*\n)/',
        function (array $matches) use ($pattern): string {
            preg_match_all(
                '/    \/\*\*\s*\n(?:     \*[^\n]*\n)+     \*\/\s*\n/',
                $matches[1],
                $blocks,
            );

            $lastBlock = end($blocks[0]);

            if (! is_string($lastBlock)) {
                return $matches[0];
            }

            if (! preg_match('/@return\s+(?:'.$pattern.')</', $lastBlock)) {
                return $matches[0];
            }

            return $lastBlock.$matches[2];
        },
        $content,
    ) ?? $content;
}

/**
 * @return array<string, string>
 */
function parseUseMap(string $content): array
{
    $map = [];
    if (! preg_match_all('/^use\s+([^;]+);/m', $content, $matches)) {
        return $map;
    }

    foreach ($matches[1] as $useStatement) {
        $useStatement = trim($useStatement);
        if (str_contains($useStatement, '{')) {
            continue;
        }

        if (str_contains($useStatement, ' as ')) {
            [$fqn, $alias] = array_map('trim', explode(' as ', $useStatement, 2));
            $map[$alias] = ltrim($fqn, '\\');
        } else {
            $parts = explode('\\', $useStatement);
            $map[end($parts)] = ltrim($useStatement, '\\');
        }
    }

    return $map;
}

function parseNamespace(string $content): ?string
{
    if (preg_match('/^namespace\s+([^;]+);/m', $content, $match)) {
        return $match[1];
    }

    return null;
}

function parseClassName(string $content): ?string
{
    if (preg_match('/class\s+(\w+)/', $content, $match)) {
        return $match[1];
    }

    return null;
}

/**
 * @param  array<string, string>  $uses
 */
function resolveRelationArgument(string $argument, string $eloquentMethod, array $uses, ?string $namespace, ?string $className): ?string
{
    $argument = trim(preg_replace('/\s+/', ' ', $argument) ?? $argument);

    if (preg_match('/\b(self|static)::class\b/', $argument) && $namespace !== null && $className !== null) {
        return '\\'.$namespace.'\\'.$className;
    }

    if (preg_match('/^([\w\\\\]+)::class\b/', $argument, $match)) {
        return resolveClass($match[1], $uses, $namespace);
    }

    if ($eloquentMethod === 'morphTo') {
        return '\\Illuminate\\Database\\Eloquent\\Model';
    }

    return null;
}

/**
 * @param  array<string, string>  $uses
 */
function resolveClass(string $class, array $uses, ?string $namespace): string
{
    $class = ltrim($class, '\\');

    if (str_contains($class, '\\')) {
        return '\\'.$class;
    }

    if (isset($uses[$class])) {
        return '\\'.$uses[$class];
    }

    if ($namespace !== null) {
        return '\\'.$namespace.'\\'.$class;
    }

    return '\\'.$class;
}
