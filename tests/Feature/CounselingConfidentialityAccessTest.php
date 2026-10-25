<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Counseling\Filament\Resources\CounselingNotes\CounselingNoteResource;
use Modules\Counseling\Models\CounselingNote;
use Modules\Counseling\Policies\CounselingNotePolicy;
use Tests\TestCase;

class CounselingConfidentialityAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_unprivileged_user_cannot_enumerate_confidential_notes_on_filament_index(): void
    {
        $tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenant);

        $public = $this->note($tenant, 'VISIBLE', false);
        $private = $this->note($tenant, 'PRIVATE', true);
        $user = User::factory()->create();
        $this->actingAs($user);

        $ids = CounselingNoteResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($public->id, $ids);
        $this->assertNotContains($private->id, $ids);
        $this->assertFalse((new CounselingNotePolicy)->view($user, $private));
    }

    public function test_global_super_admin_index_still_obeys_active_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $privateA = $this->note($tenantA, 'TENANT-A-SECRET', true);
        $privateB = $this->note($tenantB, 'TENANT-B-SECRET', true);

        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);
        app(CurrentTenant::class)->set($tenantA);

        $ids = CounselingNoteResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($privateA->id, $ids);
        $this->assertNotContains($privateB->id, $ids);
        $this->assertTrue((new CounselingNotePolicy)->view($admin, $privateA));
        $this->assertFalse((new CounselingNotePolicy)->view($admin, $privateB));
    }

    public function test_permanent_and_bulk_purge_of_counseling_notes_is_denied(): void
    {
        $tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenant);
        $user = User::factory()->superAdmin()->create();
        $note = $this->note($tenant, 'PRESERVE-HISTORY', true);
        $policy = new CounselingNotePolicy;

        $this->assertFalse($policy->deleteAny($user));
        $this->assertFalse($policy->forceDelete($user, $note));
        $this->assertFalse($policy->forceDeleteAny($user));
        $this->assertFalse($policy->restoreAny($user));
        $this->assertFalse($policy->replicate($user, $note));
        $this->assertDatabaseHas('counseling_notes', ['id' => $note->id]);
    }

    private function note(Tenant $tenant, string $code, bool $confidential): CounselingNote
    {
        return CounselingNote::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => $code,
            'name' => 'Record '.$code,
            'status' => 'active',
            'is_confidential' => $confidential,
            'body' => 'Private detail',
        ]);
    }
}
