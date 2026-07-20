<?php

namespace Tests\Feature;

use App\Integrations\Moodle\MoodleOutboxRetryService;
use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class MoodleOutboxRetryServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_retry_resets_outbox_fields(): void
    {
        Queue::fake();

        $record = $this->makeOutbox(MoodleSyncOutbox::STATUS_FAILED, [
            'attempts' => 5,
            'next_retry_at' => now()->addHour(),
            'last_error' => 'Previous failure',
            'synced_at' => now(),
        ]);

        app(MoodleOutboxRetryService::class)->retry($record);

        $record->refresh();

        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $record->status);
        $this->assertSame(0, $record->attempts);
        $this->assertNull($record->next_retry_at);
        $this->assertNull($record->last_error);
        $this->assertNull($record->synced_at);
    }

    public function test_retry_many_only_retries_failed_and_skipped_records(): void
    {
        Queue::fake();

        $failed = $this->makeOutbox(MoodleSyncOutbox::STATUS_FAILED, ['attempts' => 3], 'failed');
        $skipped = $this->makeOutbox(MoodleSyncOutbox::STATUS_SKIPPED, ['attempts' => 2], 'skipped');
        $pending = $this->makeOutbox(MoodleSyncOutbox::STATUS_PENDING, ['attempts' => 1], 'pending');
        $synced = $this->makeOutbox(MoodleSyncOutbox::STATUS_SYNCED, ['attempts' => 1], 'synced');

        $retried = app(MoodleOutboxRetryService::class)->retryMany([
            $failed,
            $skipped,
            $pending,
            $synced,
        ]);

        $this->assertSame(2, $retried);
        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $failed->fresh()->status);
        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $skipped->fresh()->status);
        $this->assertSame(0, $failed->fresh()->attempts);
        $this->assertSame(0, $skipped->fresh()->attempts);
        $this->assertSame(1, $pending->fresh()->attempts);
        $this->assertSame(MoodleSyncOutbox::STATUS_SYNCED, $synced->fresh()->status);
    }

    public function test_retry_dispatches_process_moodle_sync_outbox_job(): void
    {
        Queue::fake();

        $record = $this->makeOutbox(MoodleSyncOutbox::STATUS_SKIPPED);

        app(MoodleOutboxRetryService::class)->retry($record);

        Queue::assertPushed(ProcessMoodleSyncOutboxJob::class, function (ProcessMoodleSyncOutboxJob $job) use ($record): bool {
            return $job->outboxId === $record->id;
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeOutbox(string $status, array $attributes = [], string $dedupeSuffix = 'record'): MoodleSyncOutbox
    {
        ['tenant' => $tenant] = $this->makeTenantContext();

        return MoodleSyncOutbox::query()->create(array_merge([
            'entity_type' => 'user',
            'entity_id' => random_int(1, 100000),
            'tenant_id' => $tenant->id,
            'action' => 'upsert',
            'payload' => ['id' => 1],
            'dedupe_key' => 'retry-service-'.$dedupeSuffix.'-'.uniqid(),
            'status' => $status,
            'attempts' => 0,
            'next_retry_at' => null,
            'last_error' => null,
            'synced_at' => null,
        ], $attributes));
    }
}
