<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AppApiRoutesTest extends TestCase
{
    use LazilyRefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'name' => 'App User',
            'email' => 'app-user@example.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        $plan = SubscriptionPlan::create([
            'code' => 'app-api-plan',
            'name' => 'App API Plan',
            'included_modules' => ['core'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'app-api-tenant',
            'name' => 'App API Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($tenant);

        $created = $user->createToken('app-api-token');
        PersonalAccessToken::find($created->accessToken->id)->update(['tenant_id' => $tenant->id]);
        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_app_profile_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/app/profile')->assertUnauthorized();
    }

    public function test_app_profile_endpoint_returns_authenticated_user_resource(): void
    {
        $this->withToken($this->token)
            ->getJson('/api/v1/app/profile')
            ->assertOk()
            ->assertJsonPath('data.email', 'app-user@example.com');
    }

    public function test_app_profile_endpoint_updates_authenticated_user(): void
    {
        $this->withToken($this->token)
            ->putJson('/api/v1/app/profile', ['name' => 'Updated App User'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated App User');
    }

    #[DataProvider('protectedEndpointProvider')]
    public function test_app_endpoints_are_registered_behind_sanctum(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function protectedEndpointProvider(): array
    {
        return [
            'donations index' => ['GET', '/api/v1/app/donations'],
            'donation detail' => ['GET', '/api/v1/app/donations/1'],
            'donation checkout' => ['POST', '/api/v1/app/donations/1/checkout'],
            'my donations' => ['GET', '/api/v1/app/donations/me'],
            'products index' => ['GET', '/api/v1/app/shop/products'],
            'product detail' => ['GET', '/api/v1/app/shop/products/1'],
            'cart items' => ['POST', '/api/v1/app/shop/cart/items'],
            'shop checkout' => ['POST', '/api/v1/app/shop/checkout'],
            'my orders' => ['GET', '/api/v1/app/shop/orders/me'],
            'events index' => ['GET', '/api/v1/app/events'],
            'event detail' => ['GET', '/api/v1/app/events/1'],
            'jobs index' => ['GET', '/api/v1/app/jobs'],
            'job detail' => ['GET', '/api/v1/app/jobs/1'],
            'vouchers index' => ['GET', '/api/v1/app/vouchers'],
            'voucher claim' => ['POST', '/api/v1/app/vouchers/1/claim'],
            'voucher redeem' => ['POST', '/api/v1/app/vouchers/claims/1/redeem'],
            'notifications' => ['GET', '/api/v1/app/notifications'],
        ];
    }
}
