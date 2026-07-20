<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Counseling\Models\AnonymousReport;
use Tests\TestCase;

class ControllerScaffoldAuditTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unused_resource_scaffold_routes_are_removed(): void
    {
        $routeNames = [
            'campus.index',
            'employee.index',
            'enrollment.index',
            'finance.index',
            'global.index',
            'inventory.index',
            'library.index',
            'monitoring.index',
            'procurement.index',
            'sales.index',
            'school.index',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertFalse(Route::has($routeName), "Route [{$routeName}] should not be registered.");
        }
    }

    public function test_anonymous_report_route_accepts_valid_payload(): void
    {
        $tenant = $this->makeTenant();

        $this->postJson(route('parent.anonymous-report'), [
            'tenant_id' => $tenant->getKey(),
            'body' => 'Saya membutuhkan bantuan konseling.',
        ])->assertOk()
            ->assertJson(['accepted' => true]);

        $this->assertDatabaseHas(AnonymousReport::class, [
            'tenant_id' => $tenant->getKey(),
            'description' => 'Saya membutuhkan bantuan konseling.',
            'status' => 'new',
        ]);
    }

    public function test_anonymous_report_route_validates_payload(): void
    {
        $response = $this->postJson(route('parent.anonymous-report'), [
            'tenant_id' => null,
            'body' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonStructure([
                'error' => [
                    'details' => ['tenant_id', 'body'],
                ],
            ]);
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'scaffold-audit',
            'name' => 'Scaffold Audit',
            'included_modules' => ['core', 'counseling'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'scaffold-audit-tenant',
            'name' => 'Scaffold Audit Tenant',
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }
}
