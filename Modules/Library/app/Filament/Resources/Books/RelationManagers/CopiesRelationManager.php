<?php

namespace Modules\Library\Filament\Resources\Books\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\BookCopies\BookCopyResource;

class CopiesRelationManager extends RelationManager
{
    protected static string $relationship = 'copies';

    public function form(Schema $schema): Schema
    {
        return BookCopyResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return BookCopyResource::table($table);
    }
}
