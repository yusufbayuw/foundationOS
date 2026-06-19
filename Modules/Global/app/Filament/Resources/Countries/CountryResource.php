<?php

namespace Modules\Global\Filament\Resources\Countries;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Global\Filament\Resources\Countries\Pages\CreateCountry;
use Modules\Global\Filament\Resources\Countries\Pages\EditCountry;
use Modules\Global\Filament\Resources\Countries\Pages\ListCountries;
use Modules\Global\Filament\Resources\Countries\Pages\ViewCountry;
use Modules\Global\Filament\Resources\Countries\Schemas\CountryForm;
use Modules\Global\Filament\Resources\Countries\Schemas\CountryInfolist;
use Modules\Global\Filament\Resources\Countries\Tables\CountriesTable;
use Modules\Global\Models\Country;

class CountryResource extends LocalizedResource
{
    protected static ?string $model = Country::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CountryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CountryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CountriesTable::configure($table);
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
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'view' => ViewCountry::route('/{record}'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }
}
