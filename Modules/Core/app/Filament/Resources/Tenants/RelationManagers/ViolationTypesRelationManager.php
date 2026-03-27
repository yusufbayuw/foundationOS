<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\ViolationTypes\ViolationTypeResource;

class ViolationTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'violationTypes';

    public function form(Schema $schema): Schema
    {
        return ViolationTypeResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ViolationTypeResource::table($table);
    }
}
