<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Users\UserResource;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return UserResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return UserResource::table($table);
    }
}
