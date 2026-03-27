<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentGrades\StudentGradeResource;

class GradedStudentGradesRelationManager extends RelationManager
{
    protected static string $relationship = 'gradedStudentGrades';

    public function form(Schema $schema): Schema
    {
        return StudentGradeResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentGradeResource::table($table);
    }
}
