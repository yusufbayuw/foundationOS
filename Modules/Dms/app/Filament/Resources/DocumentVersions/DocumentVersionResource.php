<?php

namespace Modules\Dms\Filament\Resources\DocumentVersions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Dms\Filament\Resources\DocumentVersions\Pages\CreateDocumentVersion;
use Modules\Dms\Filament\Resources\DocumentVersions\Pages\EditDocumentVersion;
use Modules\Dms\Filament\Resources\DocumentVersions\Pages\ListDocumentVersions;
use Modules\Dms\Filament\Resources\DocumentVersions\Pages\ViewDocumentVersion;
use Modules\Dms\Filament\Resources\DocumentVersions\Schemas\DocumentVersionForm;
use Modules\Dms\Filament\Resources\DocumentVersions\Schemas\DocumentVersionInfolist;
use Modules\Dms\Filament\Resources\DocumentVersions\Tables\DocumentVersionsTable;
use Modules\Dms\Models\DocumentVersion;

class DocumentVersionResource extends ModuleResource
{
    protected static ?string $model = DocumentVersion::class;

    public static function form(Schema $schema): Schema
    {
        return DocumentVersionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentVersionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentVersionsTable::configure($table);
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
            'index' => ListDocumentVersions::route('/'),
            'create' => CreateDocumentVersion::route('/create'),
            'view' => ViewDocumentVersion::route('/{record}'),
            'edit' => EditDocumentVersion::route('/{record}/edit'),
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
