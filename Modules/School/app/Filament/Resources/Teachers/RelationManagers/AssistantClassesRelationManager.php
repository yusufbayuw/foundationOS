<?php

namespace Modules\School\Filament\Resources\Teachers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\SchoolClasses\SchoolClassResource;

class AssistantClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'assistantClasses';

    public function form(Schema $schema): Schema
    {
        return SchoolClassResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SchoolClassResource::table($table);
    }
}
