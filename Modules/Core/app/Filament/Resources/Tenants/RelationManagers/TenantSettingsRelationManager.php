<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantSettings\TenantSettingResource;

class TenantSettingsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenantSettings';

    public function form(Schema $schema): Schema
    {
        return TenantSettingResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TenantSettingResource::table($table);
    }
}
