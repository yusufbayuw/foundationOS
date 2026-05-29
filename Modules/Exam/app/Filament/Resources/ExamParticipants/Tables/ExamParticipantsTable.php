<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;

class ExamParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['examDefinition', 'activeToken']))
            ->columns([
                TextColumn::make('student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('examDefinition.name')
                    ->label(FilamentUi::field('exam_definition_id'))
                    ->searchable(),
                TextColumn::make('student_identifier')
                    ->label(FilamentUi::field('student_identifier'))
                    ->searchable(),
                TextColumn::make('participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->badge()
                    ->formatStateUsing(fn (?ParticipantSource $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->formatStateUsing(fn (?ParticipantStatus $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('activeToken.token')
                    ->label(FilamentUi::field('token'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('exam_definition_id')
                    ->label(FilamentUi::field('exam_definition_id'))
                    ->relationship('examDefinition', 'name')
                    ->searchable(),
                SelectFilter::make('participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->options(collect(ParticipantSource::cases())->mapWithKeys(
                        fn (ParticipantSource $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options(collect(ParticipantStatus::cases())->mapWithKeys(
                        fn (ParticipantStatus $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
