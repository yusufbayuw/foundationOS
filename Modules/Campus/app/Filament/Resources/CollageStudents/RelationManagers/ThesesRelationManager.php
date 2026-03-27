<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;

class ThesesRelationManager extends RelationManager
{
    protected static string $relationship = 'theses';

    public function form(Schema $schema): Schema
    {
        return ThesisResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ThesisResource::table($table);
    }
}
