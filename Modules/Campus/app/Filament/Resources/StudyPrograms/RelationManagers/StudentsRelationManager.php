<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function form(Schema $schema): Schema
    {
        return CollageStudentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CollageStudentResource::table($table);
    }
}
