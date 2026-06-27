<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Tables;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Support\FilamentUi;

class MoodleSyncOutboxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(FilamentUi::field('id'))
                    ->sortable(),
                TextColumn::make('tenant_id')
                    ->label(FilamentUi::field('tenant_id'))
                    ->sortable(),
                TextColumn::make('entity_type')
                    ->label(FilamentUi::field('entity_type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entity_id')
                    ->label(FilamentUi::field('entity_id'))
                    ->sortable(),
                TextColumn::make('action')
                    ->label(FilamentUi::field('action'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('attempts')
                    ->label(FilamentUi::field('attempts'))
                    ->sortable(),
                TextColumn::make('next_retry_at')
                    ->label(FilamentUi::field('next_retry_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('synced_at')
                    ->label(FilamentUi::field('synced_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_error')
                    ->label(FilamentUi::field('last_error'))
                    ->limit(80)
                    ->tooltip(fn (MoodleSyncOutbox $record): ?string => $record->last_error),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options([
                        MoodleSyncOutbox::STATUS_PENDING => FilamentUi::text('Pending'),
                        MoodleSyncOutbox::STATUS_PROCESSING => FilamentUi::text('Processing'),
                        MoodleSyncOutbox::STATUS_FAILED => FilamentUi::text('Failed'),
                        MoodleSyncOutbox::STATUS_SYNCED => FilamentUi::text('Synced'),
                        MoodleSyncOutbox::STATUS_SKIPPED => FilamentUi::text('Skipped'),
                    ]),
                SelectFilter::make('entity_type')
                    ->label(FilamentUi::field('entity_type'))
                    ->options(fn (): array => MoodleSyncOutbox::query()
                        ->distinct()
                        ->orderBy('entity_type')
                        ->pluck('entity_type', 'entity_type')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
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
            ])
            ->toolbarActions([
                BulkAction::make('retrySelected')
                    ->label(FilamentUi::text('Retry selected'))
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        /** @var Collection<int, MoodleSyncOutbox> $records */
                        $records->each(function (MoodleSyncOutbox $record): void {
                            if (! in_array($record->status, [
                                MoodleSyncOutbox::STATUS_FAILED,
                                MoodleSyncOutbox::STATUS_SKIPPED,
                            ], true)) {
                                return;
                            }

                            $record->forceFill([
                                'status' => MoodleSyncOutbox::STATUS_PENDING,
                                'attempts' => 0,
                                'next_retry_at' => null,
                                'last_error' => null,
                                'synced_at' => null,
                            ])->save();

                            ProcessMoodleSyncOutboxJob::dispatch($record->id);
                        });

                        Notification::make()
                            ->title(FilamentUi::text('Moodle sync queued'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
