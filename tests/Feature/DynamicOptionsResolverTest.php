<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\Vendor;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Enums\WorkflowGatewayType;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Services\DynamicOptionsResolver;
use Tests\TestCase;

class DynamicOptionsResolverTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenantA;

    private Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'dyn-plan',
            'name' => 'Dynamic Plan',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);

        $user = User::create([
            'name' => 'Dyn Admin',
            'email' => 'dyn-admin@example.com',
            'password' => 'password',
        ]);

        $this->tenantA = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-dyn-a',
            'name' => 'Tenant Dynamic A',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $this->tenantB = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-dyn-b',
            'name' => 'Tenant Dynamic B',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_resolves_eloquent_options_filtered_by_tenant(): void
    {
        Vendor::create(['code' => 'V-A1', 'name' => 'Alpha', 'tenant_id' => $this->tenantA->id]);
        Vendor::create(['code' => 'V-A2', 'name' => 'Beta', 'tenant_id' => $this->tenantA->id]);
        Vendor::create(['code' => 'V-B1', 'name' => 'Other Tenant', 'tenant_id' => $this->tenantB->id]);

        app(CurrentTenant::class)->set($this->tenantA);

        $resolver = new DynamicOptionsResolver;

        $options = $resolver->resolve([
            'kind' => 'eloquent',
            'model' => 'Modules\\Procurement\\Models\\Vendor',
            'label' => 'name',
            'value' => 'id',
            'tenant_aware' => true,
        ], $this->tenantA->id);

        $labels = array_column($options, 'label');

        $this->assertContains('Alpha', $labels);
        $this->assertContains('Beta', $labels);
        $this->assertNotContains('Other Tenant', $labels);
    }

    public function test_tenant_a_cannot_see_tenant_b_options(): void
    {
        Vendor::create(['code' => 'V-B2', 'name' => 'TenantB Vendor', 'tenant_id' => $this->tenantB->id]);

        app(CurrentTenant::class)->set($this->tenantA);

        $resolver = new DynamicOptionsResolver;

        $options = $resolver->resolve([
            'kind' => 'eloquent',
            'model' => 'Modules\\Procurement\\Models\\Vendor',
            'label' => 'name',
            'value' => 'id',
            'tenant_aware' => true,
        ], $this->tenantA->id);

        $labels = array_column($options, 'label');

        $this->assertNotContains('TenantB Vendor', $labels);
    }

    public function test_rejects_model_outside_whitelist(): void
    {
        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/whitelist/');

        $resolver = new DynamicOptionsResolver;
        $resolver->resolve([
            'kind' => 'eloquent',
            'model' => 'App\\Models\\User',
        ], $this->tenantA->id);
    }

    public function test_resolves_enum_options(): void
    {
        $resolver = new DynamicOptionsResolver;

        $options = $resolver->resolve([
            'kind' => 'enum',
            'class' => WorkflowGatewayType::class,
        ]);

        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }

    public function test_rejects_nonexistent_enum(): void
    {
        $this->expectException(WorkflowConfigurationException::class);

        $resolver = new DynamicOptionsResolver;
        $resolver->resolve([
            'kind' => 'enum',
            'class' => 'NonExistent\\Enum',
        ]);
    }

    public function test_rejects_unknown_kind(): void
    {
        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/kind/');

        $resolver = new DynamicOptionsResolver;
        $resolver->resolve([
            'kind' => 'graphql',
            'model' => 'Anything',
        ]);
    }

    public function test_results_are_cached_by_tenant_and_signature(): void
    {
        Cache::flush();

        Vendor::create(['code' => 'V-C1', 'name' => 'Cached Vendor', 'tenant_id' => $this->tenantA->id]);

        app(CurrentTenant::class)->set($this->tenantA);

        $resolver = new DynamicOptionsResolver;
        $source = [
            'kind' => 'eloquent',
            'model' => 'Modules\\Procurement\\Models\\Vendor',
            'label' => 'name',
            'value' => 'id',
            'tenant_aware' => true,
        ];

        $first = $resolver->resolve($source, $this->tenantA->id);
        $second = $resolver->resolve($source, $this->tenantA->id);

        $this->assertEquals($first, $second);
    }

    public function test_form_schema_validator_accepts_valid_options_source(): void
    {
        $user = User::create([
            'name' => 'Wf User',
            'email' => 'wf-dyn@example.com',
            'password' => 'password',
        ]);

        $workflow = Workflow::create([
            'tenant_id' => $this->tenantA->id,
            'code' => 'dyn-test-wf',
            'name' => 'Dynamic Test Workflow',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $step = WorkflowStep::create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $this->tenantA->id,
            'workflow_id' => $workflow->id,
            'code' => 'vendor-select',
            'name' => 'Select Vendor',
            'step_order' => 1,
            'step_type' => 'approval',
            'form_schema' => [
                [
                    'name' => 'vendor_id',
                    'type' => 'select',
                    'options_source' => [
                        'kind' => 'eloquent',
                        'model' => 'Modules\\Procurement\\Models\\Vendor',
                        'label' => 'name',
                        'value' => 'id',
                        'tenant_aware' => true,
                    ],
                ],
            ],
        ]);

        app(CurrentTenant::class)->set($this->tenantA);

        $validator = app(WorkflowFormSchemaValidator::class);
        $result = $validator->validate($step, ['vendor_id' => 1]);

        $this->assertArrayHasKey('vendor_id', $result);
    }

    public function test_form_schema_validator_rejects_non_whitelisted_model_in_options_source(): void
    {
        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/whitelist/');

        $user = User::create([
            'name' => 'Wf User 2',
            'email' => 'wf-dyn2@example.com',
            'password' => 'password',
        ]);

        $workflow = Workflow::create([
            'tenant_id' => $this->tenantA->id,
            'code' => 'dyn-test-wf2',
            'name' => 'Dynamic Test Workflow 2',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $step = WorkflowStep::create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $this->tenantA->id,
            'workflow_id' => $workflow->id,
            'code' => 'secret-step',
            'name' => 'Secret Step',
            'step_order' => 1,
            'step_type' => 'approval',
            'form_schema' => [
                [
                    'name' => 'anything',
                    'type' => 'select',
                    'options_source' => [
                        'kind' => 'eloquent',
                        'model' => 'App\\Models\\SecretModel',
                    ],
                ],
            ],
        ]);

        $validator = app(WorkflowFormSchemaValidator::class);
        $validator->validate($step, ['anything' => 1]);
    }
}
