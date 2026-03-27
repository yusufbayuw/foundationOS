<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Faculties\FacultyResource;

class FacultiesRelationManager extends RelationManager
{
    protected static string $relationship = 'faculties';

    public function form(Schema $schema): Schema
    {
        return FacultyResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return FacultyResource::table($table);
    }
}
