<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Tests\TestCase;

class ApiV2FoundationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_version_endpoint_is_public(): void
    {
        $response = $this->getJson('/api/v2');

        $response->assertOk()
            ->assertJsonPath('data.version', 'v2')
            ->assertJsonPath('data.status', 'available');
    }

    public function test_v2_me_includes_api_version_meta(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v2/me');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_v2_current_tenant_includes_api_version_meta(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'api-v2-plan',
            'name' => 'API V2 Plan',
            'included_modules' => ['core'],
        ]);

        $user = User::factory()->create();
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-v2',
            'name' => 'API V2 Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $token = $user->createToken('v2-token');
        PersonalAccessToken::query()
            ->whereKey($token->accessToken->id)
            ->update(['tenant_id' => $tenant->id]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v2/tenants/current');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v2')
            ->assertJsonPath('data.code', 'tenant-v2');
    }
}
