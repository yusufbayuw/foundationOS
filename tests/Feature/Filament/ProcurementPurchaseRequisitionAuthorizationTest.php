<?php

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\CreatePurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages\ViewPurchaseRequisition;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Tests\Concerns\BootstrapsFilamentAdmin;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class ProcurementPurchaseRequisitionAuthorizationTest extends TestCase
{
    use BootstrapsFilamentAdmin;
    use CreatesTenantForTests;
    use RefreshDatabase;

    private const MODULES = ['core', 'procurement', 'workflow'];

    protected function tearDown(): void
    {
        $this->tearDownFilamentAdmin();

        parent::tearDown();
    }

    public function test_tenant_member_without_permissions_cannot_access_list_page(): void
    {
        $this->bootstrapFilamentTenantMember(self::MODULES);

        Livewire::test(ListPurchaseRequisitions::class)
            ->assertForbidden();
    }

    public function test_tenant_member_without_permissions_cannot_access_create_page(): void
    {
        $this->bootstrapFilamentTenantMember(self::MODULES);

        Livewire::test(CreatePurchaseRequisition::class)
            ->assertForbidden();
    }

    public function test_tenant_member_without_permissions_cannot_access_view_page(): void
    {
        $context = $this->bootstrapFilamentAdmin(self::MODULES);

        $record = PurchaseRequisition::create([
            'tenant_id' => $context['tenant']->id,
            'user_id' => $context['user']->id,
            'requested_by' => $context['user']->id,
            'request_number' => 'PR-AUTH-VIEW',
            'request_date' => now()->toDateString(),
            'priority' => 'normal',
            'total_items' => 1,
            'total_estimated_amount' => 100000,
            'status' => 'draft',
        ]);

        $this->actAsFilamentTenantMember($context);

        Livewire::test(ViewPurchaseRequisition::class, ['record' => $record->getKey()])
            ->assertForbidden();
    }

    public function test_tenant_member_without_permissions_cannot_access_edit_page(): void
    {
        $context = $this->bootstrapFilamentAdmin(self::MODULES);

        $record = PurchaseRequisition::create([
            'tenant_id' => $context['tenant']->id,
            'user_id' => $context['user']->id,
            'requested_by' => $context['user']->id,
            'request_number' => 'PR-AUTH-EDIT',
            'request_date' => now()->toDateString(),
            'priority' => 'normal',
            'total_items' => 1,
            'total_estimated_amount' => 100000,
            'status' => 'draft',
        ]);

        $this->actAsFilamentTenantMember($context);

        Livewire::test(EditPurchaseRequisition::class, ['record' => $record->getKey()])
            ->assertForbidden();
    }

    public function test_resource_policy_denies_view_any_for_unprivileged_tenant_member(): void
    {
        ['user' => $user] = $this->bootstrapFilamentTenantMember(self::MODULES);

        $this->assertFalse(PurchaseRequisitionResource::canViewAny());
        $this->assertFalse($user->can('viewAny', PurchaseRequisition::class));
    }
}
