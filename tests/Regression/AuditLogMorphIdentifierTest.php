<?php

namespace Tests\Regression;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Monitoring\Models\AuditLog;
use Tests\TestCase;

class AuditLogMorphIdentifierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_numeric_and_uuid_morph_identifiers_both_preserve_their_values(): void
    {
        $tenant = Tenant::factory()->create();
        $uuid = (string) Str::uuid();

        foreach (['42', $uuid] as $identifier) {
            $audit = AuditLog::withoutTenantScope()->create([
                'tenant_id' => $tenant->getKey(),
                'auditable_type' => 'school_student',
                'auditable_id' => $identifier,
                'action' => 'test.created',
                'description' => 'Audit log identifier regression',
            ]);

            $this->assertSame($identifier, (string) $audit->fresh()->auditable_id);
        }

        $this->assertSame(
            2,
            AuditLog::withoutTenantScope()
                ->where('tenant_id', $tenant->getKey())
                ->where('action', 'test.created')
                ->count(),
        );
    }
}
