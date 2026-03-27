<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentAchievements\StudentAchievementResource;

class VerifiedStudentAchievementsRelationManager extends RelationManager
{
    protected static string $relationship = 'verifiedStudentAchievements';

    public function form(Schema $schema): Schema
    {
        return StudentAchievementResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudentAchievementResource::table($table);
    }
}
