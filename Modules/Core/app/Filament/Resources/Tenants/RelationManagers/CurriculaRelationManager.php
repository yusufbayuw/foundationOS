<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Curricula\CurriculumResource;

class CurriculaRelationManager extends RelationManager
{
    protected static string $relationship = 'curricula';

    public function form(Schema $schema): Schema
    {
        return CurriculumResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CurriculumResource::table($table);
    }
}
