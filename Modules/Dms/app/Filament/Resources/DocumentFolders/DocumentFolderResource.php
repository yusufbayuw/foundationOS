<?php

namespace Modules\Dms\Filament\Resources\DocumentFolders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Dms\Filament\Resources\DocumentFolders\Pages\CreateDocumentFolder;
use Modules\Dms\Filament\Resources\DocumentFolders\Pages\EditDocumentFolder;
use Modules\Dms\Filament\Resources\DocumentFolders\Pages\ListDocumentFolders;
use Modules\Dms\Filament\Resources\DocumentFolders\Pages\ViewDocumentFolder;
use Modules\Dms\Filament\Resources\DocumentFolders\Schemas\DocumentFolderForm;
use Modules\Dms\Filament\Resources\DocumentFolders\Schemas\DocumentFolderInfolist;
use Modules\Dms\Filament\Resources\DocumentFolders\Tables\DocumentFoldersTable;
use Modules\Dms\Models\DocumentFolder;

class DocumentFolderResource extends ModuleResource
{
    protected static ?string $model = DocumentFolder::class;

    public static function form(Schema $schema): Schema
    {
        return DocumentFolderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentFolderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentFoldersTable::configure($table);
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
            'index' => ListDocumentFolders::route('/'),
            'create' => CreateDocumentFolder::route('/create'),
            'view' => ViewDocumentFolder::route('/{record}'),
            'edit' => EditDocumentFolder::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
