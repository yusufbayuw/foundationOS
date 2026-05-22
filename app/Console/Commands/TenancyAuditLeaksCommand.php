<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenancyAuditLeaksCommand extends Command
{
    protected $signature = 'fos:tenancy:audit-leaks';

    protected $description = 'Scan all tenant_id-bearing tables for cross-tenant FK references';

    /** @var list<string> */
    protected array $globalTablesWhitelist = [
        'countries',
        'provinces',
        'cities',
        'districts',
        'villages',
        'timezones',
        'users',
        'personal_access_tokens',
        'jobs',
        'job_batches',
        'failed_jobs',
        'cache',
        'sessions',
        'migrations',
        'model_has_roles',
        'role_has_permissions',
        'permissions',
        'roles',
    ];

    public function handle(): int
    {
        $tables = $this->getTenantBearingTables();

        if (empty($tables)) {
            $this->warn('No tenant_id-bearing tables found.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Scanning %d tenant-scoped tables for cross-tenant leaks…', count($tables)));

        $leaks = [];

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);

            if (in_array('organization_id', $columns, true)) {
                $tableLeaks = $this->checkOrganizationLeak($table);

                if (! empty($tableLeaks)) {
                    $leaks[$table] = $tableLeaks;
                }
            }
        }

        if (empty($leaks)) {
            $this->info('✓ Zero cross-tenant leaks detected.');

            return self::SUCCESS;
        }

        $this->error(sprintf('✗ Cross-tenant leaks detected in %d table(s):', count($leaks)));

        foreach ($leaks as $table => $rows) {
            $this->line('');
            $this->warn(sprintf('  Table: %s (%d leak(s))', $table, count($rows)));

            foreach ($rows as $row) {
                $this->line(sprintf(
                    '    id=%s tenant_id=%s references organization.tenant_id=%s',
                    $row->id,
                    $row->row_tenant_id,
                    $row->org_tenant_id,
                ));
            }
        }

        return self::FAILURE;
    }

    /** @return list<string> */
    protected function getTenantBearingTables(): array
    {
        $allTables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = 'Tables_in_'.$dbName;

        $tenantTables = [];

        foreach ($allTables as $row) {
            $table = $row->$key;

            if (in_array($table, $this->globalTablesWhitelist, true)) {
                continue;
            }

            if (Schema::hasColumn($table, 'tenant_id')) {
                $tenantTables[] = $table;
            }
        }

        return $tenantTables;
    }

    /** @return list<object> */
    protected function checkOrganizationLeak(string $table): array
    {
        return DB::select(
            "SELECT t.id, t.tenant_id AS row_tenant_id, o.tenant_id AS org_tenant_id
             FROM `{$table}` t
             INNER JOIN organizations o ON o.id = t.organization_id
             WHERE t.organization_id IS NOT NULL
               AND t.tenant_id IS NOT NULL
               AND t.tenant_id != o.tenant_id
             LIMIT 100",
        );
    }
}
