<?php

namespace Modules\Global\Filament\Resources\Cities;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Global\Filament\Resources\Cities\Pages\CreateCity;
use Modules\Global\Filament\Resources\Cities\Pages\EditCity;
use Modules\Global\Filament\Resources\Cities\Pages\ListCities;
use Modules\Global\Filament\Resources\Cities\Pages\ViewCity;
use Modules\Global\Filament\Resources\Cities\Schemas\CityForm;
use Modules\Global\Filament\Resources\Cities\Schemas\CityInfolist;
use Modules\Global\Filament\Resources\Cities\Tables\CitiesTable;
use Modules\Global\Models\City;

class CityResource extends LocalizedResource
{
    protected static ?string $model = City::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
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
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'view' => ViewCity::route('/{record}'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
