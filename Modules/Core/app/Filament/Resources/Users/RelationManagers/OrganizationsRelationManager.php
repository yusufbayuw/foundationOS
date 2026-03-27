<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Organizations\OrganizationResource;

class OrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizations';

    public function form(Schema $schema): Schema
    {
        return OrganizationResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return OrganizationResource::table($table);
    }
}
