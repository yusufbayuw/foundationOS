<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrderItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages\CreateMerchOrderItem;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages\EditMerchOrderItem;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages\ListMerchOrderItems;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages\ViewMerchOrderItem;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Schemas\MerchOrderItemForm;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Schemas\MerchOrderItemInfolist;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\Tables\MerchOrderItemsTable;
use Modules\MerchOrder\Models\MerchOrderItem;

class MerchOrderItemResource extends ModuleResource
{
    protected static ?string $model = MerchOrderItem::class;

    public static function form(Schema $schema): Schema
    {
        return MerchOrderItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MerchOrderItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerchOrderItemsTable::configure($table);
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
            'index' => ListMerchOrderItems::route('/'),
            'create' => CreateMerchOrderItem::route('/create'),
            'view' => ViewMerchOrderItem::route('/{record}'),
            'edit' => EditMerchOrderItem::route('/{record}/edit'),
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
