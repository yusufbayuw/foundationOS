<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages\CreateAuditChecklistItem;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages\EditAuditChecklistItem;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages\ListAuditChecklistItems;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages\ViewAuditChecklistItem;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Schemas\AuditChecklistItemForm;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Schemas\AuditChecklistItemInfolist;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Tables\AuditChecklistItemsTable;
use Modules\InternalAudit\Models\AuditChecklistItem;

class AuditChecklistItemResource extends ModuleResource
{
    protected static ?string $model = AuditChecklistItem::class;

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistItemsTable::configure($table);
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
            'index' => ListAuditChecklistItems::route('/'),
            'create' => CreateAuditChecklistItem::route('/create'),
            'view' => ViewAuditChecklistItem::route('/{record}'),
            'edit' => EditAuditChecklistItem::route('/{record}/edit'),
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
