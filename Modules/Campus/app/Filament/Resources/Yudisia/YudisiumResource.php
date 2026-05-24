<?php

namespace Modules\Campus\Filament\Resources\Yudisia;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Campus\Filament\Resources\Yudisia\Pages\CreateYudisium;
use Modules\Campus\Filament\Resources\Yudisia\Pages\EditYudisium;
use Modules\Campus\Filament\Resources\Yudisia\Pages\ListYudisia;
use Modules\Campus\Filament\Resources\Yudisia\Pages\ViewYudisium;
use Modules\Campus\Filament\Resources\Yudisia\Schemas\YudisiumForm;
use Modules\Campus\Filament\Resources\Yudisia\Schemas\YudisiumInfolist;
use Modules\Campus\Filament\Resources\Yudisia\Tables\YudisiaTable;
use Modules\Campus\Models\Yudisium;
use Modules\Core\Filament\Support\ModuleResource;

class YudisiumResource extends ModuleResource
{
    protected static ?string $model = Yudisium::class;

    public static function form(Schema $schema): Schema
    {
        return YudisiumForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return YudisiumInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return YudisiaTable::configure($table);
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
            'index' => ListYudisia::route('/'),
            'create' => CreateYudisium::route('/create'),
            'view' => ViewYudisium::route('/{record}'),
            'edit' => EditYudisium::route('/{record}/edit'),
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
