<?php

namespace Modules\Global\Filament\Resources\Provinces;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Global\Filament\Resources\Provinces\Pages\CreateProvince;
use Modules\Global\Filament\Resources\Provinces\Pages\EditProvince;
use Modules\Global\Filament\Resources\Provinces\Pages\ListProvinces;
use Modules\Global\Filament\Resources\Provinces\Pages\ViewProvince;
use Modules\Global\Filament\Resources\Provinces\Schemas\ProvinceForm;
use Modules\Global\Filament\Resources\Provinces\Schemas\ProvinceInfolist;
use Modules\Global\Filament\Resources\Provinces\Tables\ProvincesTable;
use Modules\Global\Models\Province;

class ProvinceResource extends LocalizedResource
{
    protected static ?string $model = Province::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProvinceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProvinceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvincesTable::configure($table);
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
            'index' => ListProvinces::route('/'),
            'create' => CreateProvince::route('/create'),
            'view' => ViewProvince::route('/{record}'),
            'edit' => EditProvince::route('/{record}/edit'),
        ];
    }
}
