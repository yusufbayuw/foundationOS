<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;

class ExamAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'examAttempts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Attempts');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Exam attempts'))
            ->description(FilamentUi::text('Scores synced from Cloudflare runtime.'))
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['examParticipant', 'examResult'])->orderByDesc('submitted_at'))
            ->columns([
                TextColumn::make('examParticipant.student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('examParticipant.student_identifier')
                    ->label(FilamentUi::field('student_identifier'))
                    ->toggleable(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('examResult.is_passed')
                    ->label(FilamentUi::field('is_passed'))
                    ->formatStateUsing(fn (?bool $state): string => $state === null
                        ? '-'
                        : ($state ? FilamentUi::text('Yes') : FilamentUi::text('No'))),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
                TextColumn::make('submitted_at')
                    ->label(FilamentUi::field('submitted_at'))
                    ->dateTime(),
                TextColumn::make('examAnswers_count')
                    ->label(FilamentUi::field('answers'))
                    ->counts('examAnswers'),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
