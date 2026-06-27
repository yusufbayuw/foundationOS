<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Schemas;

use App\Support\TypedValue;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamDefinitionScoreCalculator;

class ExamDefinitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(FilamentUi::text('Title')),
                TextEntry::make('code')
                    ->label(FilamentUi::field('code')),
                TextEntry::make('exam_type')
                    ->label(FilamentUi::field('exam_type'))
                    ->formatStateUsing(fn (mixed $state): string => is_object($state) && method_exists($state, 'label')
                        ? FilamentUi::text(TypedValue::string($state->label()))
                        : '-'),
                TextEntry::make('exam_academic_context')
                    ->label(FilamentUi::field('exam_academic_context'))
                    ->formatStateUsing(fn (mixed $state): string => is_object($state) && method_exists($state, 'label')
                        ? FilamentUi::text(TypedValue::string($state->label()))
                        : '-'),
                TextEntry::make('status')
                    ->label(FilamentUi::field('status'))
                    ->formatStateUsing(fn (mixed $state): string => is_object($state) && method_exists($state, 'label')
                        ? FilamentUi::text(TypedValue::string($state->label()))
                        : '-')
                    ->badge(),
                TextEntry::make('question_summary')
                    ->label(FilamentUi::text('Total questions'))
                    ->state(function (ExamDefinition $record): string {
                        $calculator = app(ExamDefinitionScoreCalculator::class);

                        return (string) $calculator->questionCount($record);
                    }),
                TextEntry::make('score_summary')
                    ->label(FilamentUi::text('Total score'))
                    ->state(function (ExamDefinition $record): string {
                        $calculator = app(ExamDefinitionScoreCalculator::class);

                        return (string) $calculator->totalScore($record);
                    }),
                TextEntry::make('duration_minutes')
                    ->label(FilamentUi::field('duration_minutes')),
                TextEntry::make('starts_at')
                    ->label(FilamentUi::field('starts_at'))
                    ->dateTime(),
                TextEntry::make('ends_at')
                    ->label(FilamentUi::field('ends_at'))
                    ->dateTime(),
                IconEntry::make('shuffle_questions')
                    ->label(FilamentUi::field('shuffle_questions'))
                    ->boolean(),
                IconEntry::make('shuffle_options')
                    ->label(FilamentUi::field('shuffle_options'))
                    ->boolean(),
                IconEntry::make('show_result')
                    ->label(FilamentUi::field('show_result'))
                    ->boolean(),
                IconEntry::make('show_explanation')
                    ->label(FilamentUi::field('show_explanation'))
                    ->boolean(),
                TextEntry::make('runtime_exam_id')
                    ->label(FilamentUi::field('runtime_exam_id'))
                    ->placeholder('-'),
            ]);
    }
}
