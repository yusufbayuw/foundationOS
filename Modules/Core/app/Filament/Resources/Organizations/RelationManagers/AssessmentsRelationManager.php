<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Assessments\AssessmentResource;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    public function form(Schema $schema): Schema
    {
        return AssessmentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AssessmentResource::table($table);
    }
}
