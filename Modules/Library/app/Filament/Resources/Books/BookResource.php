<?php

namespace Modules\Library\Filament\Resources\Books;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\Books\Pages\CreateBook;
use Modules\Library\Filament\Resources\Books\Pages\EditBook;
use Modules\Library\Filament\Resources\Books\Pages\ListBooks;
use Modules\Library\Filament\Resources\Books\Pages\ViewBook;
use Modules\Library\Filament\Resources\Books\Schemas\BookForm;
use Modules\Library\Filament\Resources\Books\Schemas\BookInfolist;
use Modules\Library\Filament\Resources\Books\Tables\BooksTable;
use Modules\Library\Models\Book;

class BookResource extends LocalizedResource
{
    protected static ?string $model = Book::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BooksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBooks::route('/'),
            'create' => CreateBook::route('/create'),
            'view' => ViewBook::route('/{record}'),
            'edit' => EditBook::route('/{record}/edit'),
        ];
    }
}
