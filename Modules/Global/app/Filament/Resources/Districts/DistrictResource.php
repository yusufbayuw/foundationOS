<?php

namespace Modules\Global\Filament\Resources\Districts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Global\Filament\Resources\Districts\Pages\CreateDistrict;
use Modules\Global\Filament\Resources\Districts\Pages\EditDistrict;
use Modules\Global\Filament\Resources\Districts\Pages\ListDistricts;
use Modules\Global\Filament\Resources\Districts\Pages\ViewDistrict;
use Modules\Global\Filament\Resources\Districts\Schemas\DistrictForm;
use Modules\Global\Filament\Resources\Districts\Schemas\DistrictInfolist;
use Modules\Global\Filament\Resources\Districts\Tables\DistrictsTable;
use Modules\Global\Models\District;

class DistrictResource extends LocalizedResource
{
    protected static ?string $model = District::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DistrictForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DistrictInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DistrictsTable::configure($table);
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
            'index' => ListDistricts::route('/'),
            'create' => CreateDistrict::route('/create'),
            'view' => ViewDistrict::route('/{record}'),
            'edit' => EditDistrict::route('/{record}/edit'),
        ];
    }
}
