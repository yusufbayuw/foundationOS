#!/usr/bin/env php
<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Annotate Eloquent models for PHPStan level 10.
 * Run: php scripts/phpstan-annotate-models.php [--dry-run] [path]
 */
$dryRun = in_array('--dry-run', $argv, true);
$paths = array_values(array_filter(
    $argv,
    fn (string $arg, int $index): bool => $index > 0 && $arg !== '--dry-run' && ! str_starts_with($arg, '-'),
    ARRAY_FILTER_USE_BOTH,
));

if ($paths === []) {
    $paths = array_merge(
        glob(__DIR__.'/../app/Models/*.php') ?: [],
        glob(__DIR__.'/../Modules/*/app/Models/*.php') ?: [],
    );
}

$relationMethods = [
    'belongsTo' => 'BelongsTo',
    'hasMany' => 'HasMany',
    'hasOne' => 'HasOne',
    'belongsToMany' => 'BelongsToMany',
    'morphMany' => 'MorphMany',
    'morphOne' => 'MorphOne',
    'morphTo' => 'MorphTo',
    'hasManyThrough' => 'HasManyThrough',
];

$updated = 0;

foreach ($paths as $file) {
    $original = file_get_contents($file);
    if ($original === false) {
        continue;
    }

    $content = annotateModel($original, $relationMethods);

    if ($content !== $original) {
        if (! $dryRun) {
            file_put_contents($file, $content);
        }
        $updated++;
    }
}

echo ($dryRun ? '[dry-run] ' : '')."Updated {$updated} model file(s).\n";

/**
 * @param  array<string, string>  $relationMethods
 */
function annotateModel(string $content, array $relationMethods): string
{
    $content = dedupeRelationDocblocks($content);

    $uses = parseUseMap($content);
    $namespace = parseNamespace($content);
    $className = parseClassName($content);

    if (str_contains($content, 'HasFactory') && ! preg_match('/@use\s+HasFactory</', $content)) {
        $factoryFqn = null;

        if (preg_match('/function newFactory\(\):\s*([\w\\\\]+)/', $content, $factoryMatch)) {
            $factoryFqn = resolveClass($factoryMatch[1], $uses, $namespace);
        } elseif ($namespace !== null && $className !== null) {
            $moduleRoot = preg_replace('/\\\\Models$/', '', $namespace);
            if (is_string($moduleRoot)) {
                $factoryFqn = '\\'.$moduleRoot.'\\Database\\Factories\\'.$className.'Factory';
            }
        }

        if ($factoryFqn !== null && class_exists(ltrim($factoryFqn, '\\'))) {
            $content = preg_replace(
                '/\n(    use [^;]*HasFactory[^;]*;)/',
                "\n    /** @use HasFactory<{$factoryFqn}> */\n$1",
                $content,
                1,
            ) ?? $content;
        } elseif (str_contains($content, 'HasFactory')) {
            $content = preg_replace(
                '/\n(    use [^;]*HasFactory[^;]*;)/',
                "\n    /** @use HasFactory<\\Illuminate\\Database\\Eloquent\\Factories\\Factory<static>> */\n$1",
                $content,
                1,
            ) ?? $content;
        }
    }

    foreach ($relationMethods as $eloquentMethod => $relationClass) {
        $pattern = '/\n(?:(?:    \/\*\*[\s\S]*?\*\/\s*\n)+)?    public function (\w+)\(\): '.$relationClass.'\R    \{\R        return \$this->'.$eloquentMethod.'\(([\s\S]*?)\);\R    \}/';

        $content = preg_replace_callback($pattern, function (array $matches) use ($relationClass, $eloquentMethod, $uses, $namespace, $className): string {
            $methodBlock = $matches[0];

            if (preg_match('/@return\s+'.$relationClass.'</', $methodBlock)) {
                return $methodBlock;
            }

            $argumentBlock = $matches[2];
            $related = resolveRelationArgument($argumentBlock, $eloquentMethod, $uses, $namespace, $className);
            if ($related === null) {
                return $methodBlock;
            }

            $body = ltrim($methodBlock, "\n");
            $doc = "    /**\n     * @return {$relationClass}<{$related}, \$this>\n     */\n";

            return "\n".$doc.$body;
        }, $content) ?? $content;
    }

    return dedupeRelationDocblocks($content);
}

function dedupeRelationDocblocks(string $content): string
{
    $relationReturnPattern = 'BelongsTo|HasMany|HasOne|BelongsToMany|MorphMany|MorphOne|MorphTo|HasManyThrough';

    return preg_replace_callback(
        '/((?:    \/\*\*\s*\n(?:     \*[^\n]*\n)+     \*\/\s*\n)+)(    public function \w+\(\): (?:'.$relationReturnPattern.')\n)/',
        function (array $matches) use ($relationReturnPattern): string {
            preg_match_all(
                '/    \/\*\*\s*\n(?:     \*[^\n]*\n)+     \*\/\s*\n/',
                $matches[1],
                $blocks,
            );

            $lastBlock = end($blocks[0]);

            if (! is_string($lastBlock)) {
                return $matches[0];
            }

            if (! preg_match('/@return\s+(?:'.$relationReturnPattern.')</', $lastBlock)) {
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

/**
 * @param  array<string, string>  $uses
 * @return class-string|null
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
        return '\\'.Model::class;
    }

    return null;
}
