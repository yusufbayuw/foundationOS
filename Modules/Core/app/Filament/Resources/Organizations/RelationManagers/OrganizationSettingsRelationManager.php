<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\OrganizationSettings\OrganizationSettingResource;

class OrganizationSettingsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizationSettings';

    public function form(Schema $schema): Schema
    {
        return OrganizationSettingResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return OrganizationSettingResource::table($table);
    }
}
