<?php

namespace Modules\Workflow\Filament\Resources\Workflows\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\WorkflowTransitionResource;

class TransitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transitions';

    public function form(Schema $schema): Schema
    {
        return WorkflowTransitionResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return WorkflowTransitionResource::table($table);
    }
}
