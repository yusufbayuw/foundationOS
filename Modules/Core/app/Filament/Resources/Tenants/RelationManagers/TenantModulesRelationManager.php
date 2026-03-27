<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantModules\TenantModuleResource;

class TenantModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'tenantModules';

    public function form(Schema $schema): Schema
    {
        return TenantModuleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TenantModuleResource::table($table);
    }
}
