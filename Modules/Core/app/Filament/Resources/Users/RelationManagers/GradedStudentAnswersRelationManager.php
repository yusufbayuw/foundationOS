<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\StudentAssessmentAnswerResource;

class GradedStudentAnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'gradedStudentAnswers';

    public function form(Schema $schema): Schema
    {
        return StudentAssessmentAnswerResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentAssessmentAnswerResource::table($table);
    }
}
