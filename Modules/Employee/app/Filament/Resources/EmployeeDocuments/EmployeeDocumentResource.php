<?php

namespace Modules\Employee\Filament\Resources\EmployeeDocuments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Pages\CreateEmployeeDocument;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Pages\EditEmployeeDocument;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Pages\ListEmployeeDocuments;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Pages\ViewEmployeeDocument;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Schemas\EmployeeDocumentForm;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Schemas\EmployeeDocumentInfolist;
use Modules\Employee\Filament\Resources\EmployeeDocuments\Tables\EmployeeDocumentsTable;
use Modules\Employee\Models\EmployeeDocument;

class EmployeeDocumentResource extends ModuleResource
{
    protected static ?string $model = EmployeeDocument::class;

    public static function form(Schema $schema): Schema
    {
        return EmployeeDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeDocumentsTable::configure($table);
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
            'index' => ListEmployeeDocuments::route('/'),
            'create' => CreateEmployeeDocument::route('/create'),
            'view' => ViewEmployeeDocument::route('/{record}'),
            'edit' => EditEmployeeDocument::route('/{record}/edit'),
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
