<?php

namespace Modules\School\Filament\Resources\SchoolClasses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\ClassStudents\ClassStudentResource;

class ClassStudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'classStudents';

    public function form(Schema $schema): Schema
    {
        return ClassStudentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ClassStudentResource::table($table);
    }
}
