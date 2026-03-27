<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantRoles\TenantRoleResource;

class TenantRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'tenantRoles';

    public function form(Schema $schema): Schema
    {
        return TenantRoleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TenantRoleResource::table($table);
    }
}
