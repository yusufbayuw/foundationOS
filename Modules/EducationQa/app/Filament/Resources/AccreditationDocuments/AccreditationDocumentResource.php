<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationDocuments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages\CreateAccreditationDocument;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages\EditAccreditationDocument;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages\ListAccreditationDocuments;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages\ViewAccreditationDocument;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Schemas\AccreditationDocumentForm;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Schemas\AccreditationDocumentInfolist;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\Tables\AccreditationDocumentsTable;
use Modules\EducationQa\Models\AccreditationDocument;

class AccreditationDocumentResource extends ModuleResource
{
    protected static ?string $model = AccreditationDocument::class;

    public static function form(Schema $schema): Schema
    {
        return AccreditationDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccreditationDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccreditationDocumentsTable::configure($table);
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
            'index' => ListAccreditationDocuments::route('/'),
            'create' => CreateAccreditationDocument::route('/create'),
            'view' => ViewAccreditationDocument::route('/{record}'),
            'edit' => EditAccreditationDocument::route('/{record}/edit'),
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
