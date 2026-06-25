<?php

namespace Tests\Feature\Messaging;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Jobs\DeliverNotificationJob;
use Modules\Messaging\Services\NotificationDispatcher;
use Tests\TestCase;

class NotificationDispatcherQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_channels_are_queued_instead_of_sent_inline(): void
    {
        Queue::fake();

        [$tenant, $user] = $this->makeTenantUser();

        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'group' => 'integrations',
            'key' => 'whatsapp.enabled',
            'value' => '1',
            'type' => 'boolean',
        ]);

        $this->app->instance(WhatsAppProvider::class, new class implements WhatsAppProvider
        {
            public function sendMessage(string $to, string $body, array $variables = []): bool
            {
                return true;
            }

            public function sendTemplate(string $to, string $templateName, array $variables = []): bool
            {
                return true;
            }

            public function sendMedia(string $to, string $mediaUrl, ?string $caption = null): bool
            {
                return true;
            }
        });

        $delivery = app(NotificationDispatcher::class)->dispatch(
            $user,
            'approval',
            'Approval needed',
            'Please approve PO-1',
            ['whatsapp'],
            'queue-test-approval',
        );

        Queue::assertPushed(DeliverNotificationJob::class, function (DeliverNotificationJob $job) use ($delivery): bool {
            return $job->deliveryId === $delivery->id;
        });

        $this->assertSame('queued', $delivery->fresh()->status);
    }

    public function test_database_only_notifications_complete_synchronously(): void
    {
        Queue::fake();

        [, $user] = $this->makeTenantUser();

        $delivery = app(NotificationDispatcher::class)->dispatch(
            $user,
            'broadcast',
            'School update',
            'Hello parents',
            ['database'],
            'queue-test-database',
        );

        Queue::assertNothingPushed();
        $this->assertSame('sent', $delivery->fresh()->status);
    }

    /**
     * @return array{0: Tenant, 1: User}
     */
    private function makeTenantUser(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'messaging-queue-test'],
            ['name' => 'Messaging Queue Test', 'included_modules' => ['core', 'messaging']],
        );

        $owner = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-messaging-queue',
            'name' => 'Messaging Queue Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $owner->id,
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Member',
            'slug' => 'member',
            'permissions' => [],
            'is_default' => true,
        ]);

        $user = User::factory()->create();

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'tenant_role_id' => $role->id,
            'assigned_by' => $owner->id,
            'is_primary' => true,
        ]);

        return [$tenant, $user];
    }
}
