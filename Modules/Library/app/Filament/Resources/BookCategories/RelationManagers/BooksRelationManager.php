<?php

namespace Modules\Library\Filament\Resources\BookCategories\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Books\Schemas\BookForm;
use Modules\Library\Filament\Resources\Books\Tables\BooksTable;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    public function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BooksTable::configure($table);
    }
}
