<?php

namespace Modules\Campus\Filament\Resources\Lecturers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;

class AdvisedThesesRelationManager extends RelationManager
{
    protected static string $relationship = 'advisedTheses';

    public function form(Schema $schema): Schema
    {
        return ThesisResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ThesisResource::table($table);
    }
}
