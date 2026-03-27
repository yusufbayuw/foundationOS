<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentAchievements\StudentAchievementResource;

class StudentAchievementsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentAchievements';

    public function form(Schema $schema): Schema
    {
        return StudentAchievementResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentAchievementResource::table($table);
    }
}
