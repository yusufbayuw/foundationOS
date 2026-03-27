<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Subjects\SubjectResource;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    public function form(Schema $schema): Schema
    {
        return SubjectResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubjectResource::table($table);
    }
}
