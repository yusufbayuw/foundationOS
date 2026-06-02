<?php

namespace Tests\Feature;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MoodleOutboxAtomicClaimTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_try_claim_only_allows_one_worker(): void
    {
        $row = MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 1,
            'tenant_id' => 1,
            'action' => 'upsert',
            'payload' => ['id' => 1],
            'dedupe_key' => 'claim-test-1',
            'status' => MoodleSyncOutbox::STATUS_PENDING,
        ]);

        $first = MoodleSyncOutbox::tryClaim($row->id);
        $second = MoodleSyncOutbox::tryClaim($row->id);

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertSame(MoodleSyncOutbox::STATUS_PROCESSING, $first->fresh()->status);
    }

    public function test_sweeper_resets_stale_processing_rows(): void
    {
        $row = MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 2,
            'tenant_id' => 1,
            'action' => 'upsert',
            'payload' => ['id' => 2],
            'dedupe_key' => 'claim-test-2',
            'status' => MoodleSyncOutbox::STATUS_PROCESSING,
        ]);

        $row->forceFill(['updated_at' => now()->subHour()])->saveQuietly();

        $this->artisan('fos:moodle:sweep-stale-processing', ['--minutes' => 30])
            ->assertSuccessful();

        $this->assertSame(MoodleSyncOutbox::STATUS_PENDING, $row->fresh()->status);
    }

    public function test_job_skips_when_row_already_claimed(): void
    {
        config()->set('moodle.enabled', true);
        Queue::fake();

        $row = MoodleSyncOutbox::query()->create([
            'entity_type' => 'user',
            'entity_id' => 3,
            'tenant_id' => 1,
            'action' => 'upsert',
            'payload' => ['id' => 3],
            'dedupe_key' => 'claim-test-3',
            'status' => MoodleSyncOutbox::STATUS_PROCESSING,
        ]);

        $job = new ProcessMoodleSyncOutboxJob($row->id);
        $job->handle(
            app(\App\Integrations\Moodle\MoodleSyncService::class),
            app(\App\Integrations\Moodle\MoodleSyncRetry::class),
        );

        $this->assertSame(MoodleSyncOutbox::STATUS_PROCESSING, $row->fresh()->status);
    }
}
