<?php

namespace Modules\Capacity\Filament\Resources\CapacityResources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Capacity\Filament\Resources\CapacityResources\Pages\CreateCapacityResource;
use Modules\Capacity\Filament\Resources\CapacityResources\Pages\EditCapacityResource;
use Modules\Capacity\Filament\Resources\CapacityResources\Pages\ListCapacityResources;
use Modules\Capacity\Filament\Resources\CapacityResources\Pages\ViewCapacityResource;
use Modules\Capacity\Filament\Resources\CapacityResources\Schemas\CapacityResourceForm;
use Modules\Capacity\Filament\Resources\CapacityResources\Tables\CapacityResourcesTable;
use Modules\Capacity\Models\CapacityResource;
use Modules\Core\Filament\Support\ModuleResource;

class CapacityResourceResource extends ModuleResource
{
    protected static ?string $model = CapacityResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CapacityResourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapacityResourcesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCapacityResources::route('/'),
            'create' => CreateCapacityResource::route('/create'),
            'view' => ViewCapacityResource::route('/{record}'),
            'edit' => EditCapacityResource::route('/{record}/edit'),
        ];
    }
}
