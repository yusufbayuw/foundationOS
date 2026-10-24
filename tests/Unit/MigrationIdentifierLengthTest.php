<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class MigrationIdentifierLengthTest extends TestCase
{
    /**
     * MySQL and MariaDB limit index/constraint identifiers to 64 characters.
     * Static migration checks catch Laravel-generated names missed by SQLite CI.
     */
    public function test_literal_migration_index_and_constraint_names_fit_mysql(): void
    {
        $root = dirname(__DIR__, 2);
        $paths = [$root.'/database/migrations'];

        foreach (glob($root.'/Modules/*/database/migrations', GLOB_ONLYDIR) ?: [] as $path) {
            $paths[] = $path;
        }

        $tooLong = [];
        $checked = 0;

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
                if (! $file instanceof SplFileInfo || ! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());
                self::assertIsString($contents);
                $checked++;

                // Only literal table names are statically decidable.
                preg_match_all(
                    '/Schema::(?:create|table)\(\s*([\'"])([A-Za-z_][A-Za-z0-9_]*)\1\s*,\s*(?:static\s+)?function\s*\([^)]*\)\s*(?:use\s*\([^)]*\)\s*)?(?::\s*void\s*)?\{/s',
                    $contents,
                    $schemas,
                    PREG_OFFSET_CAPTURE,
                );

                foreach ($schemas[0] as $i => $schema) {
                    $table = $schemas[2][$i][0];
                    $body = $this->closureBody(substr($contents, $schema[1] + strlen($schema[0])));

                    preg_match_all(
                        '/\$[A-Za-z_]\w*->(index|unique|primary|fullText|spatialIndex|foreign)\s*\(\s*(\[[^\]]*\]|[\'"][^\'"]+[\'"])\s*(?:,\s*[\'"]([^\'"]+)[\'"])?\s*\)/s',
                        $body,
                        $indexes,
                        PREG_SET_ORDER,
                    );

                    foreach ($indexes as $index) {
                        $columns = $this->columnNames($index[2]);
                        $explicit = $index[3] ?? '';
                        if ($columns === [] || ($explicit === '' && str_contains($index[2], '$'))) {
                            continue;
                        }

                        $name = $explicit !== ''
                            ? $explicit
                            : strtolower(str_replace(['-', '.'], '_', $table.'_'.implode('_', $columns).'_'.strtolower($index[1])));

                        $this->recordIfTooLong($tooLong, $name, $table, $file);
                    }

                    preg_match_all(
                        '/\$[A-Za-z_]\w*->(?:nullable|uuid|nullableUuid|ulid|nullableUlid)?Morphs\s*\(\s*[\'"]([A-Za-z_][A-Za-z0-9_]*)[\'"]\s*(?:,\s*[\'"]([^\'"]+)[\'"])?\s*\)/i',
                        $body,
                        $morphs,
                        PREG_SET_ORDER,
                    );

                    foreach ($morphs as $morph) {
                        $name = $morph[2] ?? ($table.'_'.$morph[1].'_type_'.$morph[1].'_id_index');
                        $this->recordIfTooLong($tooLong, $name, $table, $file);
                    }

                    preg_match_all(
                        '/\$[A-Za-z_]\w*->foreign(?:Id|Uuid|Ulid)\s*\(\s*[\'"]([A-Za-z_][A-Za-z0-9_]*)[\'"]\s*\)(?:(?!;|\n\s*\$[A-Za-z_]\w*->).)*?->constrained\s*\(/s',
                        $body,
                        $foreignIds,
                        PREG_SET_ORDER,
                    );

                    foreach ($foreignIds as $foreignId) {
                        $this->recordIfTooLong($tooLong, $table.'_'.$foreignId[1].'_foreign', $table, $file);
                    }
                }
            }
        }

        self::assertGreaterThan(0, $checked, 'No migration files were discovered.');
        self::assertSame([], $tooLong, "MySQL identifiers exceed 64 characters:\n".implode("\n", $tooLong));
    }

    private function closureBody(string $text): string
    {
        // Tokenizer excludes braces in PHP strings/comments.
        $depth = 1;
        $content = '';

        foreach (token_get_all('<?php '.$text) as $token) {
            $piece = is_array($token) ? $token[1] : $token;
            if ($piece === '{') {
                $depth++;
            } elseif ($piece === '}') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }
            $content .= $piece;
        }

        return $content;
    }

    /** @return list<string> */
    private function columnNames(string $expression): array
    {
        if (str_starts_with(trim($expression), '[')) {
            preg_match_all('/[\'"]([A-Za-z_][A-Za-z0-9_]*)[\'"]/', $expression, $matches);

            return $matches[1];
        }

        return [trim($expression, " \t\n\r\0\x0B'\"")];
    }

    /** @param list<string> $tooLong */
    private function recordIfTooLong(array &$tooLong, string $name, string $table, SplFileInfo $file): void
    {
        if (strlen($name) > 64) {
            $tooLong[] = sprintf(
                '%s (%d characters, table %s, %s)',
                $name,
                strlen($name),
                $table,
                $file->getFilename(),
            );
        }
    }
}
