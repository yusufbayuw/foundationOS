<?php

namespace Modules\Dms\Filament\Resources\DocumentAccessLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages\CreateDocumentAccessLog;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages\EditDocumentAccessLog;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages\ListDocumentAccessLogs;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages\ViewDocumentAccessLog;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Schemas\DocumentAccessLogForm;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Schemas\DocumentAccessLogInfolist;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\Tables\DocumentAccessLogsTable;
use Modules\Dms\Models\DocumentAccessLog;

class DocumentAccessLogResource extends ModuleResource
{
    protected static ?string $model = DocumentAccessLog::class;

    public static function form(Schema $schema): Schema
    {
        return DocumentAccessLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentAccessLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentAccessLogsTable::configure($table);
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
            'index' => ListDocumentAccessLogs::route('/'),
            'create' => CreateDocumentAccessLog::route('/create'),
            'view' => ViewDocumentAccessLog::route('/{record}'),
            'edit' => EditDocumentAccessLog::route('/{record}/edit'),
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
