<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Departments\DepartmentResource;

class DepartmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function form(Schema $schema): Schema
    {
        return DepartmentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return DepartmentResource::table($table);
    }
}
