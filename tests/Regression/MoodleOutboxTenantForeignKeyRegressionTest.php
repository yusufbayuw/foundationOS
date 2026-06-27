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

    public function test_outbox_insert_fails_without_existing_tenant(): void
    {
        $this->expectException(QueryException::class);

        MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 99,
            'tenant_id' => 999_999,
            'action' => 'upsert',
            'payload' => ['id' => 99],
            'dedupe_key' => 'regression-missing-tenant',
            'status' => MoodleSyncOutbox::STATUS_PENDING,
        ]);
    }

    public function test_outbox_insert_succeeds_when_tenant_exists(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext();

        $row = MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 1,
            'tenant_id' => $tenant->id,
            'action' => 'upsert',
            'payload' => ['id' => 1],
            'dedupe_key' => 'regression-valid-tenant',
            'status' => MoodleSyncOutbox::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('moodle_sync_outbox', [
            'id' => $row->id,
            'tenant_id' => $tenant->id,
        ]);
    }

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
