<?php

namespace Modules\Global\Filament\Resources\Villages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Global\Filament\Resources\Villages\Pages\CreateVillage;
use Modules\Global\Filament\Resources\Villages\Pages\EditVillage;
use Modules\Global\Filament\Resources\Villages\Pages\ListVillages;
use Modules\Global\Filament\Resources\Villages\Pages\ViewVillage;
use Modules\Global\Filament\Resources\Villages\Schemas\VillageForm;
use Modules\Global\Filament\Resources\Villages\Schemas\VillageInfolist;
use Modules\Global\Filament\Resources\Villages\Tables\VillagesTable;
use Modules\Global\Models\Village;

class VillageResource extends LocalizedResource
{
    protected static ?string $model = Village::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VillageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VillageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillagesTable::configure($table);
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
            'index' => ListVillages::route('/'),
            'create' => CreateVillage::route('/create'),
            'view' => ViewVillage::route('/{record}'),
            'edit' => EditVillage::route('/{record}/edit'),
        ];
    }
}
