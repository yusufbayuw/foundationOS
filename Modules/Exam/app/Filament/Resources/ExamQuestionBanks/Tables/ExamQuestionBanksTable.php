<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Tables;

use App\Filament\Imports\ExamQuestionBulkImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Filament\Support\ExamQuestionImportTableActions;

class ExamQuestionBanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academic_context_type')
                    ->label(FilamentUi::field('academic_context_type'))
                    ->badge(),
                TextColumn::make('schoolSubject.name')
                    ->label(FilamentUi::field('school_subject_reference'))
                    ->toggleable(),
                TextColumn::make('campusCourse.name')
                    ->label(FilamentUi::field('campus_course_reference'))
                    ->toggleable(),
                TextColumn::make('standalone_subject')
                    ->label(FilamentUi::field('standalone_subject'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('academic_context_type')
                    ->label(FilamentUi::field('academic_context_type'))
                    ->options(collect(ExamAcademicContext::cases())->mapWithKeys(
                        fn (ExamAcademicContext $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options(collect(QuestionBankStatus::cases())->mapWithKeys(
                        fn (QuestionBankStatus $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('school_subject_reference')
                    ->label(FilamentUi::field('school_subject_reference'))
                    ->relationship('schoolSubject', 'name'),
                SelectFilter::make('campus_course_reference')
                    ->label(FilamentUi::field('campus_course_reference'))
                    ->relationship('campusCourse', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ExamQuestionImportTableActions::make(ExamQuestionBulkImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
