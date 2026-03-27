<?php

namespace Modules\Campus\Filament\Resources\Lecturers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;

class AdviseeStudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'adviseeStudents';

    public function form(Schema $schema): Schema
    {
        return CollageStudentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CollageStudentResource::table($table);
    }
}
