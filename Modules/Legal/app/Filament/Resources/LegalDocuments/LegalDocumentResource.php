<?php

namespace Modules\Legal\Filament\Resources\LegalDocuments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Legal\Filament\Resources\LegalDocuments\Pages\CreateLegalDocument;
use Modules\Legal\Filament\Resources\LegalDocuments\Pages\EditLegalDocument;
use Modules\Legal\Filament\Resources\LegalDocuments\Pages\ListLegalDocuments;
use Modules\Legal\Filament\Resources\LegalDocuments\Pages\ViewLegalDocument;
use Modules\Legal\Filament\Resources\LegalDocuments\Schemas\LegalDocumentForm;
use Modules\Legal\Filament\Resources\LegalDocuments\Tables\LegalDocumentsTable;
use Modules\Legal\Models\LegalDocument;

class LegalDocumentResource extends ModuleResource
{
    protected static ?string $model = LegalDocument::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LegalDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalDocuments::route('/'),
            'create' => CreateLegalDocument::route('/create'),
            'view' => ViewLegalDocument::route('/{record}'),
            'edit' => EditLegalDocument::route('/{record}/edit'),
        ];
    }
}
