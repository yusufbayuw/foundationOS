<?php

namespace Modules\School\Filament\Resources\Assessments\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\StudentAssessmentAnswerResource;

class StudentAssessmentAnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'studentAssessmentAnswers';

    public function form(Schema $schema): Schema
    {
        return StudentAssessmentAnswerResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentAssessmentAnswerResource::table($table);
    }
}
