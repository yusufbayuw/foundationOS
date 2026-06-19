<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\MoodleSyncService;
use App\Models\MoodleEntityMapping;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\School\Models\Schedule;

class MoodleSyncCalendarCommand extends Command
{
    protected $signature = 'fos:moodle:sync-calendar
        {--tenant= : Filter tenant_id}
        {--limit=100 : Max schedules to process}
        {--force : Recreate event even if mapping already exists}';

    protected $description = 'Sync FOS schedules into Moodle calendar events';

    public function handle(MoodleClient $client, MoodleSyncService $syncService): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        if (! config('moodle.calendar_sync_enabled', false)) {
            $this->warn('Calendar sync disabled. Set MOODLE_CALENDAR_SYNC_ENABLED=true.');

            return self::SUCCESS;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;
        $limit = max(1, (int) $this->option('limit'));
        $force = (bool) $this->option('force');

        $query = Schedule::query()->with(['schoolClass', 'subject'])->whereNull('deleted_at');
        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        $schedules = $query->limit($limit)->get();
        $created = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            $entityType = 'schedule_event';
            $mapping = MoodleEntityMapping::query()
                ->where('entity_type', $entityType)
                ->where('fos_entity_id', $schedule->id)
                ->first();

            if ($mapping && ! $force) {
                $skipped++;

                continue;
            }

            $moodleCourseId = $syncService->resolveMoodleCourseIdForClass((int) $schedule->tenant_id, (int) $schedule->class_id);
            $startAt = $this->resolveScheduleStart($schedule);
            $duration = (int) ($schedule->duration_minutes ?? 0) * 60;
            $name = trim(($schedule->subject?->name ?? 'Class Schedule').' - '.($schedule->schoolClass?->name ?? 'Class'));

            $response = $client->call('core_calendar_create_calendar_events', [
                'events' => [[
                    'name' => $name,
                    'description' => (string) ($schedule->notes ?? 'Generated from FoundationOS schedule'),
                    'format' => 1,
                    'courseid' => $moodleCourseId,
                    'eventtype' => 'course',
                    'timestart' => $startAt->timestamp,
                    'timeduration' => max(0, $duration),
                    'visible' => 1,
                ]],
            ]);

            $moodleEventId = (int) ($response['events'][0]['id'] ?? 0);

            if ($moodleEventId <= 0) {
                $skipped++;

                continue;
            }

            MoodleEntityMapping::query()->updateOrCreate(
                [
                    'entity_type' => $entityType,
                    'fos_entity_id' => (int) $schedule->id,
                ],
                [
                    'tenant_id' => (int) $schedule->tenant_id,
                    'moodle_id' => $moodleEventId,
                    'moodle_idnumber' => "fos_schedule_event_{$schedule->id}",
                    'meta' => [
                        'courseid' => $moodleCourseId,
                        'timestart' => $startAt->toDateTimeString(),
                    ],
                ],
            );

            $created++;
        }

        $this->info("Calendar sync done. Created: {$created}, skipped: {$skipped}.");

        return self::SUCCESS;
    }

    protected function resolveScheduleStart(Schedule $schedule): Carbon
    {
        $date = $schedule->effective_date
            ? Carbon::parse($schedule->effective_date)->toDateString()
            : now()->toDateString();

        $time = '08:00:00';
        if ($schedule->start_time) {
            $time = Carbon::parse($schedule->start_time)->format('H:i:s');
        }

        return Carbon::parse("{$date} {$time}", config('app.timezone', 'Asia/Jakarta'));
    }
}
