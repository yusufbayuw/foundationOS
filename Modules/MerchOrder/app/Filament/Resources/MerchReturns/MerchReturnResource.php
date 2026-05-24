<?php

namespace Modules\MerchOrder\Filament\Resources\MerchReturns;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Pages\CreateMerchReturn;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Pages\EditMerchReturn;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Pages\ListMerchReturns;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Pages\ViewMerchReturn;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Schemas\MerchReturnForm;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Schemas\MerchReturnInfolist;
use Modules\MerchOrder\Filament\Resources\MerchReturns\Tables\MerchReturnsTable;
use Modules\MerchOrder\Models\MerchReturn;

class MerchReturnResource extends ModuleResource
{
    protected static ?string $model = MerchReturn::class;

    public static function form(Schema $schema): Schema
    {
        return MerchReturnForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MerchReturnInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MerchReturnsTable::configure($table);
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
            'index' => ListMerchReturns::route('/'),
            'create' => CreateMerchReturn::route('/create'),
            'view' => ViewMerchReturn::route('/{record}'),
            'edit' => EditMerchReturn::route('/{record}/edit'),
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
