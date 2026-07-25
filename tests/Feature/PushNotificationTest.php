<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Messaging\Contracts\PushNotificationProvider;
use Modules\Messaging\DTO\PushNotificationResult;
use Modules\Messaging\Jobs\SendPushNotificationJob;
use Modules\Messaging\Models\NotificationDelivery;
use Modules\Messaging\Notifications\GenericDatabaseNotification;
use Modules\Messaging\Services\PushNotificationService;
use Tests\TestCase;

class PushNotificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'push-plan',
            'name' => 'Push Plan',
            'included_modules' => ['core', 'messaging'],
        ]);

        $this->user = User::create([
            'name' => 'Push User',
            'email' => 'push@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-push',
            'name' => 'Push Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $created = $this->user->createToken('push-token');
        $personalAccessToken = PersonalAccessToken::find($created->accessToken->id);
        $personalAccessToken->update(['tenant_id' => $this->tenant->id]);
        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    public function test_send_push_notification_job_sends_to_active_device(): void
    {
        $provider = new FakePushNotificationProvider(new PushNotificationResult(successful: true, providerMessageId: 'msg-1'));
        $this->app->instance(PushNotificationProvider::class, $provider);

        Device::create([
            'user_id' => $this->user->id,
            'token' => 'valid-token',
            'platform' => 'android',
            'is_active' => true,
        ]);

        $delivery = NotificationDelivery::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'announcement',
            'name' => 'School update',
            'status' => 'queued',
            'description' => 'Tomorrow is a holiday.',
            'meta' => [],
        ]);

        (new SendPushNotificationJob(
            userId: $this->user->id,
            title: 'School update',
            body: 'Tomorrow is a holiday.',
            data: ['category' => 'announcement'],
            notificationDeliveryId: $delivery->id,
        ))->handle(app(PushNotificationService::class));

        $this->assertSame([['valid-token', 'School update', 'Tomorrow is a holiday.', ['category' => 'announcement']]], $provider->sent);
        $this->assertDatabaseHas('notification_deliveries', [
            'id' => $delivery->id,
            'status' => 'sent',
        ]);
    }

    public function test_send_push_notification_job_deactivates_invalid_device_token(): void
    {
        $this->app->instance(PushNotificationProvider::class, new FakePushNotificationProvider(
            new PushNotificationResult(successful: false, invalidToken: true, errorMessage: 'NotRegistered'),
        ));

        $device = Device::create([
            'user_id' => $this->user->id,
            'token' => 'invalid-token',
            'platform' => 'ios',
            'is_active' => true,
        ]);

        (new SendPushNotificationJob(
            userId: $this->user->id,
            title: 'Title',
            body: 'Body',
        ))->handle(app(PushNotificationService::class));

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'is_active' => false,
        ]);
    }

    public function test_app_notification_can_be_listed_and_marked_read(): void
    {
        $this->user->notify(new GenericDatabaseNotification('Welcome', 'Hello from foundationOS.'));
        $notification = $this->user->notifications()->firstOrFail();

        $this->withToken($this->token)
            ->getJson('/api/v1/app/notifications')
            ->assertOk()
            ->assertJsonPath('data.0.id', $notification->id)
            ->assertJsonPath('data.0.title', 'Welcome')
            ->assertJsonPath('data.0.read_at', null);

        $this->withToken($this->token)
            ->postJson("/api/v1/app/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.id', $notification->id)
            ->assertJsonPath('data.title', 'Welcome');

        $this->assertNotNull($notification->refresh()->read_at);
    }
}

class FakePushNotificationProvider implements PushNotificationProvider
{
    /**
     * @var list<array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public array $sent = [];

    public function __construct(
        private PushNotificationResult $result,
    ) {}

    public function send(string $token, string $title, string $body, array $data = []): PushNotificationResult
    {
        $this->sent[] = [$token, $title, $body, $data];

        return $this->result;
    }
}
