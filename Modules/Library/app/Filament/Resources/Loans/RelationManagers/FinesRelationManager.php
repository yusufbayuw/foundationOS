<?php

namespace Modules\Library\Filament\Resources\Loans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Fines\Schemas\FineForm;
use Modules\Library\Filament\Resources\Fines\Tables\FinesTable;

class FinesRelationManager extends RelationManager
{
    protected static string $relationship = 'fines';

    public function form(Schema $schema): Schema
    {
        return FineForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return FinesTable::configure($table);
    }
}
