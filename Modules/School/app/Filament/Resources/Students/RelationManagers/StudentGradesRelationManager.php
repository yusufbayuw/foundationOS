<?php

namespace Modules\School\Filament\Resources\Students\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentGrades\StudentGradeResource;

class StudentGradesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentGrades';

    public function form(Schema $schema): Schema
    {
        return StudentGradeResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentGradeResource::table($table);
    }
}
