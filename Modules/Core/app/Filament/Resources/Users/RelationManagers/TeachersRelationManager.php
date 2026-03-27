<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Teachers\TeacherResource;

class TeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'teachers';

    public function form(Schema $schema): Schema
    {
        return TeacherResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TeacherResource::table($table);
    }
}
