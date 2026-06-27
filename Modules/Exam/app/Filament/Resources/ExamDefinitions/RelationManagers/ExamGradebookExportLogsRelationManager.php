<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\GradebookExportStatus;

class ExamGradebookExportLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'examGradebookExportLogs';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Gradebook export logs');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Gradebook export logs'))
            ->description(FilamentUi::text('History of pushes to School or Campus gradebooks.'))
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['examParticipant', 'pushedBy'])->latest())
            ->columns([
                TextColumn::make('id')
                    ->label(FilamentUi::field('id'))
                    ->copyable()
                    ->limit(8),
                TextColumn::make('target_module')
                    ->label(FilamentUi::field('target_module'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->color(fn (GradebookExportStatus $state): string => match ($state) {
                        GradebookExportStatus::Success => 'success',
                        GradebookExportStatus::Skipped => 'warning',
                        GradebookExportStatus::Failed => 'danger',
                    }),
                TextColumn::make('target_reference_type')
                    ->label(FilamentUi::field('target_reference_type'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('target_reference_id')
                    ->label(FilamentUi::field('target_reference_id'))
                    ->toggleable(),
                TextColumn::make('examParticipant.student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->toggleable(),
                TextColumn::make('error_message')
                    ->label(FilamentUi::field('error_message'))
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
