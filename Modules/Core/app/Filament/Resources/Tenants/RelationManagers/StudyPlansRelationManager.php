<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPlans\StudyPlanResource;

class StudyPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'studyPlans';

    public function form(Schema $schema): Schema
    {
        return StudyPlanResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudyPlanResource::table($table);
    }
}
