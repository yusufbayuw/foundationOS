<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Exam\Filament\Resources\ExamQuestions\ExamQuestionResource;

class ExamQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'examQuestions';

    public function form(Schema $schema): Schema
    {
        return ExamQuestionResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ExamQuestionResource::table($table);
    }
}
