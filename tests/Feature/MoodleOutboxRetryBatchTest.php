<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxRetryService;
use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Jobs\RetryMoodleSyncOutboxBatchJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Tables\MoodleSyncOutboxesTable;
use Tests\Concerns\BootstrapsFilamentAdmin;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class MoodleOutboxRetryBatchTest extends TestCase
{
    use BootstrapsFilamentAdmin;
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        $this->tearDownFilamentAdmin();

        parent::tearDown();
    }

    public function test_bulk_action_dispatches_retry_batch_job_for_eligible_records(): void
    {
        ['tenant' => $tenant] = $this->bootstrapFilamentAdmin(['core', 'monitoring']);

        config()->set('moodle.queue', 'moodle-test');
        config()->set('moodle.batch_limit', 2);
        Queue::fake();

        $failed = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_FAILED, 'bulk-failed');
        $skipped = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_SKIPPED, 'bulk-skipped');
        $synced = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_SYNCED, 'bulk-synced');

        MoodleSyncOutboxesTable::queueRetryBatch(new Collection([$failed, $skipped, $synced]));

        Queue::assertPushed(RetryMoodleSyncOutboxBatchJob::class, function (RetryMoodleSyncOutboxBatchJob $job, string $queue) use ($failed, $skipped): bool {
            return $queue === 'moodle-test'
                && $job->ids === [(int) $failed->id, (int) $skipped->id];
        });

        Queue::assertNotPushed(ProcessMoodleSyncOutboxJob::class);
    }

    public function test_retry_batch_job_dispatches_per_record_process_jobs(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext();

        config()->set('moodle.queue', 'moodle-test');
        config()->set('moodle.batch_limit', 1);
        Queue::fake();

        $failed = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_FAILED, 'job-failed');
        $skipped = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_SKIPPED, 'job-skipped');
        $synced = $this->outbox($tenant->id, MoodleSyncOutbox::STATUS_SYNCED, 'job-synced');

        (new RetryMoodleSyncOutboxBatchJob([
            (int) $failed->id,
            (int) $skipped->id,
            (int) $synced->id,
        ]))->handle(app(MoodleOutboxRetryService::class));

        Queue::assertPushed(ProcessMoodleSyncOutboxJob::class, 2);
        Queue::assertPushed(ProcessMoodleSyncOutboxJob::class, fn (ProcessMoodleSyncOutboxJob $job, string $queue): bool => $queue === 'moodle-test' && $job->outboxId === (int) $failed->id);
        Queue::assertPushed(ProcessMoodleSyncOutboxJob::class, fn (ProcessMoodleSyncOutboxJob $job, string $queue): bool => $queue === 'moodle-test' && $job->outboxId === (int) $skipped->id);

        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $failed->fresh()->status);
        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $skipped->fresh()->status);
        $this->assertSame(MoodleSyncOutbox::STATUS_SYNCED, $synced->fresh()->status);
    }

    private function outbox(int $tenantId, string $status, string $dedupeKey): MoodleSyncOutbox
    {
        return MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => random_int(1, 100000),
            'tenant_id' => $tenantId,
            'action' => 'upsert',
            'payload' => ['id' => 1],
            'dedupe_key' => $dedupeKey,
            'status' => $status,
            'attempts' => 3,
            'last_error' => 'Previous failure',
        ]);
    }
}
