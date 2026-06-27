<?php

namespace Modules\Library\Filament\Resources\Books;

use App\Support\TypedValue;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Library\Filament\Resources\Books\Pages\CreateBook;
use Modules\Library\Filament\Resources\Books\Pages\EditBook;
use Modules\Library\Filament\Resources\Books\Pages\ListBooks;
use Modules\Library\Filament\Resources\Books\Pages\ViewBook;
use Modules\Library\Filament\Resources\Books\RelationManagers\AuditLogsRelationManager;
use Modules\Library\Filament\Resources\Books\RelationManagers\AuthorItemsRelationManager;
use Modules\Library\Filament\Resources\Books\RelationManagers\CopiesRelationManager;
use Modules\Library\Filament\Resources\Books\RelationManagers\FileUploadsRelationManager;
use Modules\Library\Filament\Resources\Books\RelationManagers\ReservationsRelationManager;
use Modules\Library\Filament\Resources\Books\RelationManagers\SubjectItemsRelationManager;
use Modules\Library\Filament\Resources\Books\Schemas\BookForm;
use Modules\Library\Filament\Resources\Books\Schemas\BookInfolist;
use Modules\Library\Filament\Resources\Books\Tables\BooksTable;
use Modules\Library\Filament\Resources\LibraryResource as LocalizedResource;
use Modules\Library\Models\Book;

class BookResource extends LocalizedResource
{
    use ConfiguresGlobalSearch;

    protected static ?string $model = Book::class;

    protected static ?string $recordTitleAttribute = 'title';

    /**
     * @return array<int, string>
     */
    protected static function globalSearchAttributes(): array
    {
        return ['title', 'isbn', 'isbn13'];
    }

    /**
     * @return array<string, string>
     */
    protected static function globalSearchResultDetails(Model $record): array
    {
        $status = $record->getAttribute('status');

        return static::detailStatus($status === null ? null : TypedValue::string($status));
    }

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
            CopiesRelationManager::class,
            ReservationsRelationManager::class,
            AuthorItemsRelationManager::class,
            SubjectItemsRelationManager::class,
            AuditLogsRelationManager::class,
            FileUploadsRelationManager::class,
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
