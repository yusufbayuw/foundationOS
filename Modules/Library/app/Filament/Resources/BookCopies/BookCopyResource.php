<?php

namespace Modules\Library\Filament\Resources\BookCopies;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\BookCopies\Pages\CreateBookCopy;
use Modules\Library\Filament\Resources\BookCopies\Pages\EditBookCopy;
use Modules\Library\Filament\Resources\BookCopies\Pages\ListBookCopies;
use Modules\Library\Filament\Resources\BookCopies\Pages\ViewBookCopy;
use Modules\Library\Filament\Resources\BookCopies\RelationManagers\LoansRelationManager;
use Modules\Library\Filament\Resources\BookCopies\Schemas\BookCopyForm;
use Modules\Library\Filament\Resources\BookCopies\Schemas\BookCopyInfolist;
use Modules\Library\Filament\Resources\BookCopies\Tables\BookCopiesTable;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\BookCopy;

class BookCopyResource extends LocalizedResource
{
    protected static ?string $model = BookCopy::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BookCopyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookCopyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookCopiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LoansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookCopies::route('/'),
            'create' => CreateBookCopy::route('/create'),
            'view' => ViewBookCopy::route('/{record}'),
            'edit' => EditBookCopy::route('/{record}/edit'),
        ];
    }
}
