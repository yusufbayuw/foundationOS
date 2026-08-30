<?php

namespace Tests\Feature\Security;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WriteEndpointSecurityConfigurationTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function write_api_endpoints_use_form_requests_policy_authorization_and_idempotency(): void
    {
        $this->assertStringContainsString("Route::middleware(['abilities:api:write', 'idempotency', 'throttle:checkout'])->group", file_get_contents(__DIR__.'/../../../routes/api.php'));

        foreach ([
            'StoreApplicantRequest' => 'Applicant::class',
            'StorePaymentRequest' => 'Payment::class',
            'StoreLeaveRequestRequest' => 'LeaveRequest::class',
            'StoreDeviceRequest' => 'Device::class',
        ] as $request => $policyTarget) {
            $contents = file_get_contents(__DIR__."/../../../app/Http/Requests/Api/V1/{$request}.php");

            $this->assertStringContainsString("->can('create', {$policyTarget})", $contents);
        }
    }

    #[Test]
    public function every_authenticated_write_api_route_requires_an_explicit_token_ability(): void
    {
        $authenticatedWriteRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (LaravelRoute $route): bool => str_starts_with($route->uri(), 'api/'))
            ->filter(fn (LaravelRoute $route): bool => array_intersect(
                ['POST', 'PUT', 'PATCH', 'DELETE'],
                $route->methods(),
            ) !== [])
            ->filter(fn (LaravelRoute $route): bool => in_array(
                'auth:sanctum',
                $route->gatherMiddleware(),
                true,
            ) || in_array(
                'Illuminate\Auth\Middleware\Authenticate:sanctum',
                $route->gatherMiddleware(),
                true,
            ));

        $this->assertNotEmpty($authenticatedWriteRoutes);

        foreach ($authenticatedWriteRoutes as $route) {
            $requiredAbility = $route->uri() === 'api/exam/runtime/attempts'
                ? 'exam:attempts:write'
                : 'api:write';

            $this->assertNotEmpty(
                array_intersect([
                    'abilities:'.$requiredAbility,
                    CheckAbilities::class.':'.$requiredAbility,
                ], $route->gatherMiddleware()),
                "Authenticated write route [{$route->uri()}] is missing token ability [{$requiredAbility}].",
            );
        }
    }

    #[Test]
    public function exam_runtime_write_route_requires_tenant_context_and_shield_permission(): void
    {
        $route = Route::getRoutes()->getByName('api.exam.runtime.attempts.store');

        $this->assertNotNull($route);
        $this->assertNotEmpty(array_intersect([
            'resolve.api.tenant',
            'App\Http\Middleware\ResolveApiTenant',
        ], $route->gatherMiddleware()));
        $this->assertNotEmpty(array_intersect([
            'permission:sync_exam_result',
            'Spatie\Permission\Middleware\PermissionMiddleware:sync_exam_result',
        ], $route->gatherMiddleware()));
    }

    #[Test]
    public function read_only_token_cannot_call_a_general_write_endpoint(): void
    {
        $token = $this->tenantToken(['api:read']);

        $this->withToken($token)->postJson('/api/v1/devices', [
            'token' => 'read-only-device-token',
            'platform' => 'web',
        ])->assertForbidden();

        $this->assertDatabaseMissing('devices', ['token' => 'read-only-device-token']);
    }

    #[Test]
    public function general_write_token_cannot_ingest_exam_attempts(): void
    {
        $token = $this->tenantToken(['api:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', [])
            ->assertForbidden();
    }

    #[Test]
    public function payment_proof_upload_is_private_and_validated(): void
    {
        $request = file_get_contents(__DIR__.'/../../../app/Http/Requests/Api/V1/StorePaymentRequest.php');
        $form = file_get_contents(__DIR__.'/../../../Modules/Finance/app/Filament/Resources/Payments/Schemas/PaymentForm.php');

        $this->assertStringContainsString('mimetypes:application/pdf,image/jpeg,image/png', $request);
        $this->assertStringContainsString('extensions:pdf,jpg,jpeg,png', $request);
        $this->assertStringContainsString('max:5120', $request);
        $this->assertStringContainsString("->visibility('private')", $form);
    }

    /**
     * @param  list<string>  $abilities
     */
    private function tenantToken(array $abilities): string
    {
        $user = User::query()->create([
            'name' => 'Ability Test User',
            'email' => Str::uuid().'@example.test',
            'password' => bcrypt('password'),
        ]);
        $plan = SubscriptionPlan::query()->create([
            'code' => 'ability-plan-'.Str::lower(Str::random(8)),
            'name' => 'Ability Test Plan',
            'included_modules' => ['core', 'exam'],
        ]);
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'ability-tenant-'.Str::lower(Str::random(8)),
            'name' => 'Ability Test Tenant',
            'subscription_plan_id' => $plan->getKey(),
            'created_by' => $user->getKey(),
        ]);
        $createdToken = $user->createToken('ability-test', $abilities);

        PersonalAccessToken::query()
            ->findOrFail($createdToken->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        return $createdToken->plainTextToken;
    }
}
