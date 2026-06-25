<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Filament\Support\FilamentResourceEagerLoads;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Tests\TestCase;

class FilamentResourceEagerLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_resource_eager_loads_reduce_tenant_and_organization_queries(): void
    {
        $tenant = $this->makeTenant();
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_active' => true,
            'is_main' => true,
        ]);
        $user = User::factory()->create();

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'title' => 'Query Test Book',
            'authors' => ['Author'],
        ]);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'member_number' => 'MEM-001',
        ]);

        $copy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'book_id' => $book->id,
            'copy_number' => 1,
            'barcode' => 'BC-001',
        ]);

        for ($i = 0; $i < 5; $i++) {
            Loan::create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'book_copy_id' => $copy->id,
                'member_id' => $member->id,
                'loan_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
            ]);
        }

        $withoutEager = $this->countQueries(function () use ($tenant): void {
            $loans = Loan::withoutTenantScope()
                ->where('tenant_id', $tenant->id)
                ->limit(5)
                ->get();

            foreach ($loans as $loan) {
                $loan->tenant?->name;
                $loan->organization?->name;
            }
        });

        $withEager = $this->countQueries(function () use ($tenant): void {
            $query = Loan::withoutTenantScope()->where('tenant_id', $tenant->id);
            FilamentResourceEagerLoads::apply($query, Loan::class);

            $loans = $query->limit(5)->get();

            foreach ($loans as $loan) {
                $loan->tenant?->name;
                $loan->organization?->name;
            }
        });

        $this->assertSame(11, $withoutEager, 'Expected 1 list query + 5 tenant + 5 organization lazy loads.');
        $this->assertSame(3, $withEager, 'Expected 1 list query + 1 tenant eager + 1 organization eager load.');
        $this->assertLessThan($withoutEager, $withEager);
    }

    public function test_module_resource_query_includes_default_eager_loads(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        PurchaseRequisition::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => 'PR-DB-001',
            'request_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $queryCount = $this->countQueries(function () use ($tenant): void {
            $requisition = PurchaseRequisitionResource::getEloquentQuery()
                ->withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->first();

            $this->assertNotNull($requisition);
            $requisition->tenant?->name;
            $requisition->user?->name;
        });

        $this->assertSame(3, $queryCount);
    }

    public function test_workflow_instance_table_scope_reduces_relation_queries(): void
    {
        $tenant = $this->makeTenant();
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_active' => true,
            'is_main' => true,
        ]);
        $requester = User::factory()->create();

        $workflow = Workflow::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'db-layer-test',
            'name' => 'DB Layer Test Workflow',
            'module' => 'procurement',
            'subject_type' => 'purchase_requisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Active,
            'is_active' => true,
        ]);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'review',
            'name' => 'Review',
            'step_type' => 'approval',
            'assignee_type' => WorkflowAssigneeType::User,
            'assignee_value' => (string) $requester->id,
            'sla_hours' => 8,
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        for ($i = 0; $i < 3; $i++) {
            WorkflowInstance::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'workflow_id' => $workflow->id,
                'workflow_version' => 1,
                'workflow_snapshot' => ['code' => 'db-layer-test'],
                'current_step_id' => $step->id,
                'requester_id' => $requester->id,
                'started_by' => $requester->id,
                'subject_type' => 'purchase_requisition',
                'subject_id' => $i + 1,
                'subject_label' => 'PR-'.$i,
                'status' => WorkflowInstanceStatus::Running,
                'started_at' => now(),
            ]);
        }

        $withoutEager = $this->countQueries(function () use ($tenant): void {
            $instances = WorkflowInstance::withoutTenantScope()
                ->where('tenant_id', $tenant->id)
                ->limit(3)
                ->get();

            foreach ($instances as $instance) {
                $instance->workflow?->name;
                $instance->currentStep?->name;
                $instance->requester?->name;
            }
        });

        $withEager = $this->countQueries(function () use ($tenant): void {
            $instances = WorkflowInstance::withoutTenantScope()
                ->where('tenant_id', $tenant->id)
                ->withTableRelations()
                ->limit(3)
                ->get();

            foreach ($instances as $instance) {
                $instance->workflow?->name;
                $instance->currentStep?->name;
                $instance->requester?->name;
            }
        });

        $this->assertSame(10, $withoutEager, 'Expected 1 list query + 3x3 lazy relation loads.');
        $this->assertSame(4, $withEager, 'Expected 1 list query + 3 eager relation loads.');
        $this->assertLessThan($withoutEager, $withEager);
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'db-layer-'.Str::lower(Str::random(6)),
            'name' => 'DB Layer Plan',
            'included_modules' => ['core', 'library', 'workflow'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'db-layer-'.Str::lower(Str::random(6)),
            'name' => 'DB Layer Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }

    private function countQueries(callable $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        return count(DB::getQueryLog());
    }
}
