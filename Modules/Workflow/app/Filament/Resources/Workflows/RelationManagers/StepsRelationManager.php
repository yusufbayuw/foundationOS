<?php

namespace Modules\Workflow\Filament\Resources\Workflows\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Workflow\Filament\Resources\WorkflowSteps\WorkflowStepResource;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    public function form(Schema $schema): Schema
    {
        return WorkflowStepResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return WorkflowStepResource::table($table);
    }
}
