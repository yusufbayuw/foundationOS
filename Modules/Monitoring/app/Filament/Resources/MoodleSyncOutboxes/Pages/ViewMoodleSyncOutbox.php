<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\MoodleSyncOutboxResource;

class ViewMoodleSyncOutbox extends ViewRecord
{
    protected static string $resource = MoodleSyncOutboxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label(FilamentUi::text('Retry sync'))
                ->visible(fn (MoodleSyncOutbox $record): bool => in_array($record->status, [
                    MoodleSyncOutbox::STATUS_FAILED,
                    MoodleSyncOutbox::STATUS_SKIPPED,
                ], true))
                ->requiresConfirmation()
                ->action(function (MoodleSyncOutbox $record): void {
                    $record->forceFill([
                        'status' => MoodleSyncOutbox::STATUS_PENDING,
                        'attempts' => 0,
                        'next_retry_at' => null,
                        'last_error' => null,
                        'synced_at' => null,
                    ])->save();

                    ProcessMoodleSyncOutboxJob::dispatch($record->id);

                    Notification::make()
                        ->title(FilamentUi::text('Moodle sync queued'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
