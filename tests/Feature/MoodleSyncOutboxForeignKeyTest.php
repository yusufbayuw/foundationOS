<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MoodleSyncOutboxForeignKeyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_moodle_sync_outbox_tenant_id_has_foreign_key_to_tenants(): void
    {
        $foreignKeys = Schema::getForeignKeys('moodle_sync_outbox');
        $tenantFk = collect($foreignKeys)->first(
            fn (array $fk): bool => in_array('tenant_id', $fk['columns'] ?? [], true),
        );

        $this->assertNotNull($tenantFk);
        $this->assertSame('tenants', $tenantFk['foreign_table'] ?? null);
    }
}
