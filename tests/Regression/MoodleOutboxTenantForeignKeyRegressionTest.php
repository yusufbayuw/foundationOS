<?php

namespace Tests\Regression;

use App\Models\MoodleSyncOutbox;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Regression: Moodle outbox rows require a valid tenant_id (FK to tenants).
 *
 * @see tests/Queue/MoodleOutboxAtomicClaimTest.php
 */
class MoodleOutboxTenantForeignKeyRegressionTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_schema_enforces_tenant_foreign_key(): void
    {
        $foreignKeys = Schema::getForeignKeys('moodle_sync_outbox');
        $tenantFk = collect($foreignKeys)->first(
            fn (array $fk): bool => in_array('tenant_id', $fk['columns'] ?? [], true),
        );

        $this->assertNotNull($tenantFk);
        $this->assertSame('tenants', $tenantFk['foreign_table'] ?? null);
    }
}
