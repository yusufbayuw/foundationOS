<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TenantMigrationService
{
    private const TABLES = [
        'tenants' => 'id',
        'organizations' => 'tenant_id',
    ];

    /**
     * @return array{tenant_id: int, tables: array<string, array{row_count: int, checksum: string, rows: list<array<string, mixed>>}>}
     */
    public function export(int $tenantId): array
    {
        $tables = [];

        foreach (self::TABLES as $table => $tenantColumn) {
            $query = DB::table($table)->orderBy('id');

            if ($table === 'tenants') {
                $query->where('id', $tenantId);
            } else {
                $query->where($tenantColumn, $tenantId);
            }

            /** @var list<array<string, mixed>> $rows */
            $rows = $query->get()
                ->map(static function (object $row): array {
                    /** @var array<string, mixed> $data */
                    $data = (array) $row;

                    return $data;
                })
                ->values()
                ->all();

            $tables[$table] = [
                'row_count' => count($rows),
                'checksum' => $this->checksum($rows),
                'rows' => $rows,
            ];
        }

        return ['tenant_id' => $tenantId, 'tables' => $tables];
    }

    /**
     * @param  array{tables: array<string, array{row_count: int, checksum: string}>}  $export
     * @return array{matches: bool, tables: array<string, array{row_count: int, checksum: string, expected_checksum: string, matches: bool}>}
     */
    public function verify(int $tenantId, array $export): array
    {
        $current = $this->export($tenantId);
        $tables = [];

        foreach ($current['tables'] as $table => $snapshot) {
            $expected = $export['tables'][$table] ?? ['row_count' => -1, 'checksum' => ''];

            $tables[$table] = [
                'row_count' => $snapshot['row_count'],
                'checksum' => $snapshot['checksum'],
                'expected_checksum' => $expected['checksum'],
                'matches' => $snapshot['row_count'] === $expected['row_count']
                    && hash_equals($snapshot['checksum'], $expected['checksum']),
            ];
        }

        return [
            'matches' => collect($tables)->every(fn (array $table): bool => $table['matches']),
            'tables' => $tables,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function checksum(array $rows): string
    {
        return hash('sha256', json_encode($rows, JSON_THROW_ON_ERROR));
    }
}
