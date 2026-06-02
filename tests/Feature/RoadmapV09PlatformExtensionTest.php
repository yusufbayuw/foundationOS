<?php

namespace Tests\Feature;

use App\Jobs\DeliverWebhookJob;
use App\Services\TenantMigrationService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\WhatsAppProvider;
use Modules\Messaging\Services\NotificationDispatcher;
use Modules\Monitoring\Models\WebhookDelivery;
use Modules\Monitoring\Models\WebhookSubscription;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowDelegation;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Services\WorkflowEscalationService;
use Tests\TestCase;

class RoadmapV09PlatformExtensionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_whatsapp_dispatch_is_feature_flagged_idempotent_and_uses_mock_provider(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();
        $user = $this->makeTenantUser($tenant, $organization, ['phone' => '+628123456789']);
        $provider = new class implements WhatsAppProvider
        {
            /** @var list<array{to: string, body: string}> */
            public array $messages = [];

            public function sendMessage(string $to, string $body, array $variables = []): bool
            {
                $this->messages[] = compact('to', 'body');

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
        };

        $this->app->instance(WhatsAppProvider::class, $provider);
        TenantSetting::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'group' => 'integrations',
            'key' => 'whatsapp.enabled',
            'value' => '1',
            'type' => 'boolean',
        ]);

        $first = app(NotificationDispatcher::class)->dispatch(
            $user,
            'approval',
            'Approval needed',
            'Please approve PO-1',
            ['whatsapp'],
            'approval-po-1',
        );

        $second = app(NotificationDispatcher::class)->dispatch(
            $user,
            'approval',
            'Approval needed',
            'Please approve PO-1',
            ['whatsapp'],
            'approval-po-1',
        );

        $this->assertSame($first->id, $second->id);
        $this->assertCount(1, $provider->messages);
        $this->assertDatabaseHas('notification_deliveries', [
            'id' => $first->id,
            'status' => 'sent',
            'idempotency_key' => 'approval-po-1',
        ]);
    }

    public function test_whatsapp_webhook_requires_valid_signature(): void
    {
        config(['messaging.webhooks.whatsapp_secret' => 'secret']);
        $payload = ['from' => '+628123456789', 'text' => 'APPROVE PO-1'];
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = 'sha256='.hash_hmac('sha256', $body, 'secret');

        $this->postJson('/api/webhooks/whatsapp/meta', $payload, [
            'X-Hub-Signature-256' => $signature,
        ])->assertOk()->assertJsonPath('verified', true);

        $this->postJson('/api/webhooks/whatsapp/meta', $payload, [
            'X-Hub-Signature-256' => 'sha256=bad',
        ])->assertForbidden();
    }

    public function test_webhook_delivery_signs_payload_and_schedules_retry(): void
    {
        Queue::fake();
        [$tenant] = $this->makeTenantWithOrganization();

        $subscription = WebhookSubscription::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.test/webhook',
            'events' => ['student.created'],
            'secret' => 'webhook-secret',
            'is_active' => true,
        ]);

        $delivery = WebhookDelivery::query()->create([
            'webhook_subscription_id' => $subscription->id,
            'event' => 'student.created',
            'payload' => ['student_id' => 10],
            'status' => 'pending',
        ]);

        Http::fake(function ($request) use ($delivery) {
            $expected = 'sha256='.hash_hmac('sha256', json_encode(['student_id' => 10], JSON_UNESCAPED_SLASHES), 'webhook-secret');

            $this->assertSame($expected, $request->header('X-Hub-Signature-256')[0] ?? null);
            $this->assertSame((string) $delivery->id, $request->header('X-Delivery-Id')[0] ?? null);

            return Http::response(['ok' => false], 500);
        });

        app()->call([new DeliverWebhookJob($delivery->id), 'handle']);

        $delivery->refresh();

        $this->assertSame('failed', $delivery->status);
        $this->assertSame(1, $delivery->attempt_count);
        $this->assertNotNull($delivery->next_retry_at);
    }

    public function test_public_api_publishes_openapi_contract(): void
    {
        $this->getJson('/api/openapi.json')
            ->assertOk()
            ->assertJsonPath('openapi', '3.1.0')
            ->assertJsonPath('paths./api/v1/organizations.get.security.0.sanctum.0', 'organizations:read');
    }

    public function test_tenant_migration_export_import_has_row_count_and_checksum_parity(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();
        $export = app(TenantMigrationService::class)->export($tenant->id);
        $verification = app(TenantMigrationService::class)->verify($tenant->id, $export);

        $this->assertTrue($verification['matches']);
        $this->assertSame(1, $verification['tables']['organizations']['row_count']);
        $this->assertSame($organization->id, $export['tables']['organizations']['rows'][0]['id']);
    }

    public function test_workflow_delegation_and_escalation_cover_sla_edge_case(): void
    {
        [$tenant, $organization] = $this->makeTenantWithOrganization();
        $from = $this->makeTenantUser($tenant, $organization, ['email' => 'from@example.test']);
        $to = $this->makeTenantUser($tenant, $organization, ['email' => 'to@example.test']);

        WorkflowDelegation::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'from_user_id' => $from->id,
            'to_user_id' => $to->id,
            'valid_from' => now('Asia/Jakarta')->subDay(),
            'valid_until' => now('Asia/Jakarta')->addDay(),
            'workflow_type_codes' => ['purchase_approval'],
            'is_active' => true,
        ]);

        $workflow = Workflow::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'purchase_approval',
            'name' => 'Purchase Approval',
            'module' => 'procurement',
            'subject_type' => 'purchase_order',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Active,
            'is_active' => true,
        ]);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) str()->uuid(),
            'code' => 'approval',
            'name' => 'Approval',
            'step_type' => 'approval',
            'assignee_type' => WorkflowAssigneeType::User,
            'assignee_value' => (string) $from->id,
            'sla_hours' => 8,
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        $instance = WorkflowInstance::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'workflow_id' => $workflow->id,
            'workflow_version' => 1,
            'workflow_snapshot' => ['code' => 'purchase_approval', 'version' => 1],
            'current_step_id' => $step->id,
            'requester_id' => $from->id,
            'started_by' => $from->id,
            'subject_type' => 'purchase_order',
            'subject_id' => 1,
            'subject_label' => 'PO-1',
            'status' => WorkflowInstanceStatus::Running,
            'started_at' => now('Asia/Jakarta')->subDay(),
        ]);

        $assignment = app(WorkflowEscalationService::class)->createAssignmentWithDelegation($instance, $step, $from);

        $this->assertSame($to->id, $assignment->assigned_to_id);
        $this->assertSame('delegation', $assignment->meta['delegated_reason'] ?? null);

        $assignment->update(['due_at' => now('Asia/Jakarta')->subMinute()]);

        $escalated = app(WorkflowEscalationService::class)->escalateOverdue(now('Asia/Jakarta'));

        $this->assertSame(1, $escalated);
        $this->assertDatabaseHas('workflow_assignments', [
            'id' => $assignment->id,
            'status' => WorkflowAssignmentStatus::Pending->value,
        ]);
        $this->assertSame('overdue', $assignment->refresh()->meta['sla_state'] ?? null);
    }

    public function test_pwa_and_native_shell_artifacts_exist_for_ci(): void
    {
        $this->assertFileExists(public_path('manifest.webmanifest'));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(base_path('mobile/capacitor.config.json'));
        $this->assertFileExists(base_path('.github/workflows/mobile-shell.yml'));
    }

    /**
     * @return array{0: Tenant, 1: Organization}
     */
    private function makeTenantWithOrganization(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'enterprise-v09',
            'name' => 'Enterprise V09',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'included_modules' => [],
            'features' => [],
            'is_active' => true,
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) str()->uuid(),
            'code' => 'T'.str()->upper(str()->random(6)),
            'name' => 'Yayasan Platform',
            'status' => 'active',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'ORG',
            'name' => 'Unit Platform',
            'type' => 'school',
            'is_active' => true,
        ]);

        return [$tenant, $organization];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeTenantUser(Tenant $tenant, Organization $organization, array $attributes = []): User
    {
        $user = User::query()->create(array_merge([
            'name' => 'Platform User',
            'email' => str()->random(8).'@example.test',
            'password' => 'password',
            'status' => 'active',
        ], $attributes));

        $role = TenantRole::withoutTenantScope()->firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'slug' => 'operator',
            ],
            [
                'name' => 'Operator',
                'permissions' => [],
                'is_default' => true,
            ],
        );

        $user->userTenantRoles()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_at' => now(),
            'is_primary' => true,
        ]);

        return $user;
    }
}
