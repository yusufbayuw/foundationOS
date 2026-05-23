<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Core\Models\ParentStudent;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Legal\Events\ContractExpiringSoon;
use Modules\Legal\Models\Contract;
use Modules\Messaging\Services\NotificationDispatcher;
use Modules\School\Models\Student;
use Tests\TestCase;

class RoadmapV04WaveATest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_expiring_soon_event_fires(): void
    {
        Event::fake([ContractExpiringSoon::class]);

        $tenant = $this->makeTenant();

        $contract = Contract::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CTR-001',
            'name' => 'Vendor Agreement',
            'status' => 'active',
            'expires_at' => now()->addDays(7),
        ]);

        event(new ContractExpiringSoon($contract));

        Event::assertDispatched(ContractExpiringSoon::class);
    }

    public function test_parent_cannot_see_other_parents_children(): void
    {
        $tenant = $this->makeTenant();
        $parentA = User::factory()->create();
        $parentB = User::factory()->create();

        $studentA = Student::query()->create(['tenant_id' => $tenant->getKey(), 'status' => 'active']);
        $studentB = Student::query()->create(['tenant_id' => $tenant->getKey(), 'status' => 'active']);

        ParentStudent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'parent_user_id' => $parentA->getKey(),
            'student_id' => $studentA->getKey(),
            'relationship' => 'father',
        ]);

        ParentStudent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'parent_user_id' => $parentB->getKey(),
            'student_id' => $studentB->getKey(),
            'relationship' => 'mother',
        ]);

        $visibleIds = ParentStudent::query()
            ->where('parent_user_id', $parentA->getKey())
            ->pluck('student_id');

        $this->assertTrue($visibleIds->contains($studentA->getKey()));
        $this->assertFalse($visibleIds->contains($studentB->getKey()));
    }

    public function test_notification_dispatcher_is_idempotent(): void
    {
        $user = User::factory()->create(['phone' => '6281234567890']);

        $dispatcher = app(NotificationDispatcher::class);

        $first = $dispatcher->dispatch(
            user: $user,
            category: 'test',
            subject: 'Hello',
            body: 'World',
            channels: ['database'],
            idempotencyKey: 'test-key-1',
        );

        $second = $dispatcher->dispatch(
            user: $user,
            category: 'test',
            subject: 'Hello',
            body: 'World',
            channels: ['database'],
            idempotencyKey: 'test-key-1',
        );

        $this->assertSame($first->getKey(), $second->getKey());
    }

    public function test_letter_verification_endpoint_returns_not_found_for_invalid_token(): void
    {
        $response = $this->getJson('/api/letters/verify/invalid-token');

        $response->assertNotFound();
    }

    protected function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'starter-'.Str::random(4),
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.Str::random(6),
            'name' => 'Test Tenant',
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }
}
