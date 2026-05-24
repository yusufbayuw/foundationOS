<?php

namespace Modules\MerchOrder\Filament\Resources\MerchPickups;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Pages\CreateMerchPickup;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Pages\EditMerchPickup;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Pages\ListMerchPickups;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Pages\ViewMerchPickup;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Schemas\MerchPickupForm;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Schemas\MerchPickupInfolist;
use Modules\MerchOrder\Filament\Resources\MerchPickups\Tables\MerchPickupsTable;
use Modules\MerchOrder\Models\MerchPickup;

class MerchPickupResource extends ModuleResource
{
    protected static ?string $model = MerchPickup::class;

    public static function form(Schema $schema): Schema
    {
        return MerchPickupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MerchPickupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerchPickupsTable::configure($table);
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
            'index' => ListMerchPickups::route('/'),
            'create' => CreateMerchPickup::route('/create'),
            'view' => ViewMerchPickup::route('/{record}'),
            'edit' => EditMerchPickup::route('/{record}/edit'),
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
