<?php

namespace Modules\Dms\Filament\Resources\Documents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Dms\Filament\Resources\Documents\Pages\CreateDocument;
use Modules\Dms\Filament\Resources\Documents\Pages\EditDocument;
use Modules\Dms\Filament\Resources\Documents\Pages\ListDocuments;
use Modules\Dms\Filament\Resources\Documents\Pages\ViewDocument;
use Modules\Dms\Filament\Resources\Documents\Schemas\DocumentForm;
use Modules\Dms\Filament\Resources\Documents\Tables\DocumentsTable;
use Modules\Dms\Models\Document;

class DocumentResource extends ModuleResource
{
    protected static ?string $model = Document::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'view' => ViewDocument::route('/{record}'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
