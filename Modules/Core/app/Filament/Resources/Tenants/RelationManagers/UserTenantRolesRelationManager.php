<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;

class UserTenantRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'userTenantRoles';

    public function form(Schema $schema): Schema
    {
        return UserTenantRoleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return UserTenantRoleResource::table($table);
    }
}
