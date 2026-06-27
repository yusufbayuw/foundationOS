<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;

class ExamActivityLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'examActivityLogs';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Activity Logs');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Exam activity logs'))
            ->description(FilamentUi::text('Proctoring and session events from the runtime.'))
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['examParticipant', 'examAttempt'])->orderByDesc('occurred_at'))
            ->columns([
                TextColumn::make('id')
                    ->label(FilamentUi::field('activity_id'))
                    ->copyable()
                    ->limit(8)
                    ->tooltip(fn (string $state): string => $state),
                TextColumn::make('examParticipant.student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('event_type')
                    ->label(FilamentUi::field('event_type'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('occurred_at')
                    ->label(FilamentUi::field('occurred_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('examAttempt.id')
                    ->label(FilamentUi::field('attempt_id'))
                    ->limit(8)
                    ->toggleable(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
