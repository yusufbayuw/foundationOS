<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\Vendor;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ViewWorkflowInstance;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Tests\TestCase;

class WorkflowDynamicFormRenderingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_workflow_action_form_renders_dynamic_and_native_filament_fields(): void
    {
        $user = User::factory()->create();
        $plan = SubscriptionPlan::create([
            'code' => 'dynamic-rendering',
            'name' => 'Dynamic Rendering',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'dynamic-rendering',
            'name' => 'Dynamic Rendering',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);
        $vendor = Vendor::create([
            'tenant_id' => $tenant->id,
            'code' => 'V-DYNAMIC',
            'name' => 'Dynamic Vendor',
        ]);

        app(CurrentTenant::class)->set($tenant);

        $step = new WorkflowStep([
            'form_schema' => [
                [
                    'name' => 'vendor_id',
                    'label' => 'Vendor',
                    'type' => 'select',
                    'options_source' => [
                        'kind' => 'eloquent',
                        'model' => Vendor::class,
                        'label' => 'name',
                        'value' => 'id',
                        'tenant_aware' => true,
                    ],
                ],
                [
                    'name' => 'grade',
                    'label' => 'Grade',
                    'type' => 'radio',
                    'options_source' => [
                        'kind' => 'static',
                        'options' => [
                            ['value' => 'A', 'label' => 'Excellent'],
                            ['value' => 'B', 'label' => 'Good'],
                        ],
                    ],
                ],
                [
                    'name' => 'reviewed_at',
                    'label' => 'Reviewed at',
                    'type' => 'datetime',
                ],
                [
                    'name' => 'confirmed',
                    'label' => 'Confirmed',
                    'type' => 'checkbox',
                ],
            ],
        ]);
        $instance = new WorkflowInstance([
            'tenant_id' => $tenant->id,
            'workflow_version' => 1,
            'status' => 'running',
        ]);
        $instance->setRelation('currentStep', $step);

        $page = new class extends ViewWorkflowInstance
        {
            public function dynamicSchema(WorkflowInstance $record): array
            {
                return $this->buildDynamicFormSchema($record);
            }
        };

        $schema = $page->dynamicSchema($instance);

        $this->assertInstanceOf(Select::class, $schema[0]);
        $this->assertSame(
            [$vendor->id => 'Dynamic Vendor'],
            $schema[0]->getSearchResults('Dynamic'),
        );
        $this->assertInstanceOf(Radio::class, $schema[1]);
        $this->assertSame(['A' => 'Excellent', 'B' => 'Good'], $schema[1]->getOptions());
        $this->assertInstanceOf(DateTimePicker::class, $schema[2]);
        $this->assertInstanceOf(Checkbox::class, $schema[3]);

        $this->assertCount(5, $schema);
    }
}
