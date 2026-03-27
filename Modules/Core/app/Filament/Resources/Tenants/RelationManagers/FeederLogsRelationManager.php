<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\FeederLogs\FeederLogResource;

class FeederLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'feederLogs';

    public function form(Schema $schema): Schema
    {
        return FeederLogResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return FeederLogResource::table($table);
    }
}
