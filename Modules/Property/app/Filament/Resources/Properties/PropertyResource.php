<?php

namespace Modules\Property\Filament\Resources\Properties;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\Properties\Pages\CreateProperty;
use Modules\Property\Filament\Resources\Properties\Pages\EditProperty;
use Modules\Property\Filament\Resources\Properties\Pages\ListProperties;
use Modules\Property\Filament\Resources\Properties\Pages\ViewProperty;
use Modules\Property\Filament\Resources\Properties\Schemas\PropertyForm;
use Modules\Property\Filament\Resources\Properties\Schemas\PropertyInfolist;
use Modules\Property\Filament\Resources\Properties\Tables\PropertiesTable;
use Modules\Property\Models\Property;

class PropertyResource extends ModuleResource
{
    protected static ?string $model = Property::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PropertyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PropertyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'view' => ViewProperty::route('/{record}'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }
}
