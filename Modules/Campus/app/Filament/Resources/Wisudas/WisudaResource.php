<?php

namespace Modules\Campus\Filament\Resources\Wisudas;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Campus\Filament\Resources\Wisudas\Pages\CreateWisuda;
use Modules\Campus\Filament\Resources\Wisudas\Pages\EditWisuda;
use Modules\Campus\Filament\Resources\Wisudas\Pages\ListWisudas;
use Modules\Campus\Filament\Resources\Wisudas\Pages\ViewWisuda;
use Modules\Campus\Filament\Resources\Wisudas\Schemas\WisudaForm;
use Modules\Campus\Filament\Resources\Wisudas\Schemas\WisudaInfolist;
use Modules\Campus\Filament\Resources\Wisudas\Tables\WisudasTable;
use Modules\Campus\Models\Wisuda;
use Modules\Core\Filament\Support\ModuleResource;

class WisudaResource extends ModuleResource
{
    protected static ?string $model = Wisuda::class;

    public static function form(Schema $schema): Schema
    {
        return WisudaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WisudaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WisudasTable::configure($table);
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
            'index' => ListWisudas::route('/'),
            'create' => CreateWisuda::route('/create'),
            'view' => ViewWisuda::route('/{record}'),
            'edit' => EditWisuda::route('/{record}/edit'),
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
