<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPlanItems\StudyPlanItemResource;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return StudyPlanItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudyPlanItemResource::table($table);
    }
}
