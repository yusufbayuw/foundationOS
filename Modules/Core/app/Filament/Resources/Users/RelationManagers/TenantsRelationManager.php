<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Tenants\TenantResource;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    public function form(Schema $schema): Schema
    {
        return TenantResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TenantResource::table($table);
    }
}
