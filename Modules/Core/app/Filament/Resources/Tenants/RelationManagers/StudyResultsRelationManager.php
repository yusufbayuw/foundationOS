<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyResults\StudyResultResource;

class StudyResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'studyResults';

    public function form(Schema $schema): Schema
    {
        return StudyResultResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudyResultResource::table($table);
    }
}
