<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\AchievementTypes\AchievementTypeResource;

class AchievementTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'achievementTypes';

    public function form(Schema $schema): Schema
    {
        return AchievementTypeResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AchievementTypeResource::table($table);
    }
}
