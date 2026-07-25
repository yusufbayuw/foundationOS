<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages;

use App\Integrations\Moodle\MoodleOutboxRetryService;
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
                ->action(function (MoodleSyncOutbox $record, MoodleOutboxRetryService $retryService): void {
                    $retryService->retry($record);

                    Notification::make()
                        ->title(FilamentUi::text('Moodle sync queued'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
