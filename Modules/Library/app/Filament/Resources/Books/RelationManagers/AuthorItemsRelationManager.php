<?php

namespace Modules\Library\Filament\Resources\Books\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryAuthors\LibraryAuthorResource;

class AuthorItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'authorItems';

    public function form(Schema $schema): Schema
    {
        return LibraryAuthorResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LibraryAuthorResource::table($table);
    }
}
