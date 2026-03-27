<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;

class CollageStudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'collageStudents';

    public function form(Schema $schema): Schema
    {
        return CollageStudentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CollageStudentResource::table($table);
    }
}
