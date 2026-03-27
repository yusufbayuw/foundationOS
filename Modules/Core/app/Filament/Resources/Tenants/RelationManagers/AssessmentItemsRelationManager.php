<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\AssessmentItems\AssessmentItemResource;

class AssessmentItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessmentItems';

    public function form(Schema $schema): Schema
    {
        return AssessmentItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AssessmentItemResource::table($table);
    }
}
