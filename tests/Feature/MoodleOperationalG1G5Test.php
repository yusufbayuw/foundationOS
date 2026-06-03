<?php

namespace Tests\Feature;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use App\Integrations\Moodle\Exceptions\MoodleReadonlySkipException;
use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\MoodleMapper;
use App\Integrations\Moodle\MoodleSyncRetry;
use App\Integrations\Moodle\MoodleSyncService;
use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Mockery;
use Tests\TestCase;

class MoodleOperationalG1G5Test extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_enroll_user_treats_already_enrolled_as_success(): void
    {
        $service = Mockery::mock(MoodleSyncService::class, [
            Mockery::mock(MoodleClient::class),
            new MoodleMapper,
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $service->shouldReceive('callMoodle')
            ->once()
            ->with('enrol_manual_enrol_users', Mockery::type('array'))
            ->andThrow(new MoodleIntegrationException('User is already enrolled in the course'));

        $service->enrollUser(101, 202);

        $this->addToAssertionCount(1);
    }

    public function test_readonly_mode_throws_skip_exception_on_moodle_call(): void
    {
        config(['moodle.readonly' => true]);

        $this->expectException(MoodleReadonlySkipException::class);

        app(MoodleSyncService::class)->enrollUser(101, 202);
    }

    public function test_readonly_job_marks_outbox_as_skipped_not_synced(): void
    {
        config([
            'moodle.enabled' => true,
            'moodle.readonly' => true,
        ]);

        $outbox = MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 1,
            'tenant_id' => 1,
            'action' => 'upsert',
            'payload' => ['email' => 'student@example.test'],
            'dedupe_key' => 'user:1:upsert:readonly-test',
            'status' => MoodleSyncOutbox::STATUS_PENDING,
        ]);

        $syncService = Mockery::mock(MoodleSyncService::class)->makePartial();
        $syncService->shouldReceive('syncOutboxItem')
            ->once()
            ->andThrow(new MoodleReadonlySkipException);

        $job = new ProcessMoodleSyncOutboxJob($outbox->id);
        $job->handle($syncService, app(MoodleSyncRetry::class));

        $outbox->refresh();

        $this->assertSame(MoodleSyncOutbox::STATUS_SKIPPED, $outbox->status);
        $this->assertNull($outbox->synced_at);
        $this->assertStringContainsString('READONLY', (string) $outbox->last_error);
    }

    public function test_health_check_fails_when_readonly_in_production(): void
    {
        config([
            'moodle.enabled' => true,
            'moodle.base_url' => 'https://moodle.example.test',
            'moodle.token' => 'token',
            'moodle.readonly' => true,
        ]);

        app()->detectEnvironment(fn (): string => 'production');

        $this->artisan('fos:moodle:health-check')
            ->assertFailed();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
