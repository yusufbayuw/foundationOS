<?php

declare(strict_types=1);
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';

$migrationFiles = array_merge(
    glob(__DIR__.'/../database/migrations/*.php') ?: [],
    glob(__DIR__.'/../Modules/*/database/migrations/*.php') ?: [],
);

function extractCreateBlocks(string $content): array
{
    $tables = [];
    if (! preg_match_all("/Schema::create\(\s*'([^']+)'/", $content, $matches, PREG_OFFSET_CAPTURE)) {
        return $tables;
    }

    foreach ($matches[1] as $i => $m) {
        $table = $m[0];
        $start = $matches[0][$i][1];
        $sub = substr($content, $start);
        if (! preg_match('/function\s*\([^)]*\)\s*(?::\s*void)?\s*\{/', $sub, $fn, PREG_OFFSET_CAPTURE)) {
            continue;
        }
        $bodyStart = $fn[0][1] + strlen($fn[0][0]);
        $depth = 1;
        $len = strlen($sub);
        $body = '';
        for ($j = $bodyStart; $j < $len; $j++) {
            $ch = $sub[$j];
            if ($ch === '{') {
                $depth++;
            }
            if ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }
            $body .= $ch;
        }
        $tables[$table] = $body;
    }

    return $tables;
}

function parseColumn(string $line): ?array
{
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '//') || str_starts_with($line, '/*')) {
        return null;
    }

    if (! preg_match('/\$table->([a-zA-Z0-9_]+)\((.*)\)(.*);/s', $line, $m)) {
        return null;
    }

    $method = $m[1];
    $args = $m[2];
    $chain = $m[3];
    $nullable = str_contains($chain, '->nullable()');
    $default = null;
    if (preg_match("/->default\(([^)]+)\)/", $chain, $dm)) {
        $default = trim($dm[1], " '\"");
    }
    $unique = str_contains($chain, '->unique()') || $method === 'unique';

    if ($method === 'timestamps') {
        return ['columns' => [
            ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
            ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
        ]];
    }

    if ($method === 'softDeletes') {
        return ['columns' => [
            ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
        ]];
    }

    if ($method === 'morphs') {
        $base = trim($args, "'\"");

        return ['columns' => [
            ['name' => $base.'_type', 'type' => 'string'],
            ['name' => $base.'_id', 'type' => 'unsignedBigInteger'],
        ]];
    }

    if (in_array($method, ['unique', 'index', 'primary', 'foreign'], true)) {
        return ['constraint' => $method, 'raw' => $line];
    }

    $col = null;
    $type = $method;
    $firstArg = trim(explode(',', $args)[0], "'\"");

    match ($method) {
        'id' => [$col, $type] = ['id', 'bigIncrements'],
        'uuid' => [$col, $type] = [$firstArg, 'uuid'],
        'foreignId' => [$col, $type] = [$firstArg, 'foreignId'],
        'foreignUuid' => [$col, $type] = [$firstArg, 'foreignUuid'],
        'foreignIdFor' => [$col, $type] = [preg_replace('/::class.*/', '', $args).'_id', 'foreignIdFor'],
        default => $col = $firstArg,
    };

    if (! $col) {
        return ['raw' => $line];
    }

    return ['columns' => [[
        'name' => $col,
        'type' => $type,
        'nullable' => $nullable,
        'default' => $default,
        'unique' => $unique,
    ]]];
}

$allTables = [];
foreach ($migrationFiles as $file) {
    $content = file_get_contents($file);
    foreach (extractCreateBlocks($content) as $table => $body) {
        $lines = preg_split('/\r\n|\r|\n/', $body);
        $cols = [];
        $constraints = [];
        foreach ($lines as $line) {
            $parsed = parseColumn(trim($line));
            if (! $parsed) {
                continue;
            }
            if (isset($parsed['columns'])) {
                $cols = array_merge($cols, $parsed['columns']);
            } else {
                $constraints[] = $parsed;
            }
        }
        $allTables[$table] = [
            'file' => str_replace(__DIR__.'/../', '', $file),
            'columns' => $cols,
            'constraints' => $constraints,
        ];
    }
}

// Map models to tables
$modelFiles = array_merge(
    glob(__DIR__.'/../app/Models/*.php') ?: [],
    glob(__DIR__.'/../Modules/*/app/Models/**/*.php') ?: [],
    glob(__DIR__.'/../Modules/*/app/Models/*.php') ?: [],
);

$models = [];
foreach ($modelFiles as $file) {
    $content = file_get_contents($file);
    if (! preg_match('/namespace\s+([^;]+);/', $content, $ns)) {
        continue;
    }
    if (! preg_match('/class\s+(\w+)/', $content, $cls)) {
        continue;
    }
    $table = null;
    if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)/', $content, $tm)) {
        $table = $tm[1];
    } else {
        $table = Str::snake(Str::pluralStudly($cls[1]));
    }
    $relations = [];
    if (preg_match_all('/public\s+function\s+(\w+)\([^)]*\)\s*:\s*([^\n{]+)/', $content, $rm, PREG_SET_ORDER)) {
        foreach ($rm as $r) {
            $name = $r[1];
            $return = trim($r[2]);
            if (preg_match('/(BelongsTo|HasMany|HasOne|BelongsToMany|MorphTo|MorphMany|MorphOne)/', $return)) {
                $relations[] = ['name' => $name, 'return' => $return];
            }
        }
    }
    $fillable = [];
    if (preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\];/s', $content, $fm)) {
        preg_match_all("/'([^']+)'/", $fm[1], $fills);
        $fillable = $fills[1] ?? [];
    }
    $models[$table] = [
        'class' => $ns[1].'\\'.$cls[1],
        'file' => str_replace(__DIR__.'/../', '', $file),
        'fillable' => $fillable,
        'relations' => $relations,
    ];
}

$technical = ['cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions', 'password_reset_tokens', 'migrations', 'notifications', 'imports', 'exports', 'failed_import_rows', 'personal_access_tokens', 'pulse_values', 'pulse_entries', 'pulse_aggregates', 'activity_log', 'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions'];

$lookup = ['countries', 'provinces', 'cities', 'districts', 'villages', 'timezones', 'chart_of_accounts', 'tuition_types', 'procurement_categories', 'achievement_types', 'violation_types', 'book_categories', 'library_member_types', 'library_item_statuses', 'library_gmds', 'library_publishers', 'library_authors', 'library_collection_types', 'library_locations', 'library_frequencies', 'ticket_categories', 'letter_categories', 'risk_categories', 'ai_prompt_templates', 'modules', 'subscription_plans'];

$output = [
    'table_count' => count($allTables),
    'model_count' => count($models),
    'tables' => $allTables,
    'models' => $models,
    'technical' => array_values(array_intersect(array_keys($allTables), $technical)),
    'lookup' => array_values(array_intersect(array_keys($allTables), $lookup)),
];

file_put_contents(__DIR__.'/../storage/app/entity-catalog.json', json_encode($output, JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/../docs/catalogs/entity-catalog.json', json_encode($output, JSON_PRETTY_PRINT));
echo 'Tables: '.count($allTables)."\n";
echo 'Models: '.count($models)."\n";
echo 'Written to storage/app/entity-catalog.json and docs/catalogs/entity-catalog.json'."\n";
