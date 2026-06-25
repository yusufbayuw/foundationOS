<?php

declare(strict_types=1);
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$files = array_merge(
    glob(__DIR__.'/../database/migrations/*.php') ?: [],
    glob(__DIR__.'/../Modules/*/database/migrations/*.php') ?: [],
);

$results = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $rel = str_replace(__DIR__.'/../', '', $file);

    // Tables created in this file
    if (! preg_match_all("/Schema::create\(\s*'([^']+)'/", $content, $tableMatches)) {
        $tableMatches[1] = ['__alter_only__'];
    }

    foreach ($tableMatches[1] as $tableName) {
        // foreignId(...)->constrained('table')
        if (preg_match_all(
            "/\\\$table->foreignId\(\s*'([^']+)'\s*\)(.*?);/s",
            $content,
            $rows,
            PREG_SET_ORDER
        )) {
            foreach ($rows as $row) {
                $column = $row[1];
                $chain = $row[2];
                if (! preg_match('/->constrained\(/', $chain)) {
                    continue;
                }
                $references = null;
                if (preg_match("/->constrained\(\s*'([^']+)'\s*\)/", $chain, $refMatch)) {
                    $references = $refMatch[1];
                } elseif (preg_match('/->constrained\(\s*\)/', $chain)) {
                    $references = '(implicit: '.inferTable($column).')';
                }
                $onDelete = 'restrict';
                if (str_contains($chain, 'cascadeOnDelete')) {
                    $onDelete = 'cascade';
                } elseif (str_contains($chain, 'nullOnDelete')) {
                    $onDelete = 'set null';
                }

                $results[] = [
                    'table' => $tableName === '__alter_only__' ? basename($file, '.php') : $tableName,
                    'column' => $column,
                    'references' => $references,
                    'on_delete' => $onDelete,
                    'source' => $rel,
                    'verified' => true,
                ];
            }
        }

        // $table->foreign('col')->references('id')->on('table')
        if (preg_match_all(
            "/\\\$table->foreign\(\s*'([^']+)'\s*\)\s*->references\(\s*'([^']+)'\s*\)\s*->on\(\s*'([^']+)'\s*\)(.*?);/s",
            $content,
            $rows,
            PREG_SET_ORDER
        )) {
            foreach ($rows as $row) {
                $chain = $row[4];
                $onDelete = 'restrict';
                if (str_contains($chain, 'cascadeOnDelete')) {
                    $onDelete = 'cascade';
                } elseif (str_contains($chain, 'nullOnDelete')) {
                    $onDelete = 'set null';
                }
                $results[] = [
                    'table' => $tableName === '__alter_only__' ? basename($file, '.php') : $tableName,
                    'column' => $row[1],
                    'references' => $row[3],
                    'on_delete' => $onDelete,
                    'source' => $rel,
                    'verified' => true,
                ];
            }
        }
    }
}

function inferTable(string $column): string
{
    if (str_ends_with($column, '_id')) {
        return Str::plural(substr($column, 0, -3));
    }

    return '?';
}

file_put_contents(__DIR__.'/../storage/app/verified-fks.json', json_encode($results, JSON_PRETTY_PRINT));
file_put_contents(__DIR__.'/../docs/catalogs/verified-fks.json', json_encode($results, JSON_PRETTY_PRINT));
echo 'Verified FKs: '.count($results)."\n";

$hub = array_filter($results, fn ($r) => in_array($r['references'], ['tenants', 'organizations', 'users'], true));
echo 'FKs to tenants/orgs/users: '.count($hub)."\n";
