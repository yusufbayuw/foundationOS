<?php

namespace Modules\Library\Filament\Resources\BookCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Filament\Resources\BookCategories\Pages\CreateBookCategory;
use Modules\Library\Filament\Resources\BookCategories\Pages\EditBookCategory;
use Modules\Library\Filament\Resources\BookCategories\Pages\ListBookCategories;
use Modules\Library\Filament\Resources\BookCategories\Pages\ViewBookCategory;
use Modules\Library\Filament\Resources\BookCategories\RelationManagers\BooksRelationManager;
use Modules\Library\Filament\Resources\BookCategories\Schemas\BookCategoryForm;
use Modules\Library\Filament\Resources\BookCategories\Schemas\BookCategoryInfolist;
use Modules\Library\Filament\Resources\BookCategories\Tables\BookCategoriesTable;
use Modules\Library\Models\BookCategory;

class BookCategoryResource extends LocalizedResource
{
    protected static ?string $model = BookCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BookCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BooksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookCategories::route('/'),
            'create' => CreateBookCategory::route('/create'),
            'view' => ViewBookCategory::route('/{record}'),
            'edit' => EditBookCategory::route('/{record}/edit'),
        ];
    }
}
