<?php

namespace Modules\Campus\Filament\Resources\Lecturers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;

class ExaminedThesesRelationManager extends RelationManager
{
    protected static string $relationship = 'examinedTheses';

    public function form(Schema $schema): Schema
    {
        return ThesisResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ThesisResource::table($table);
    }
}
