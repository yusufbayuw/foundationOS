<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamQuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('examQuestionBank.name')
                    ->label(FilamentUi::field('exam_question_bank_id')),
                TextEntry::make('type')
                    ->label(FilamentUi::field('type')),
                TextEntry::make('topic')
                    ->label(FilamentUi::field('topic')),
                TextEntry::make('difficulty')
                    ->label(FilamentUi::field('difficulty')),
                TextEntry::make('score')
                    ->label(FilamentUi::field('score')),
                TextEntry::make('status')
                    ->label(FilamentUi::field('status')),
            ]);
    }
}
