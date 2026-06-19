<?php

namespace Modules\Core\Filament\Resources\Modules;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Modules\Pages\CreateModule;
use Modules\Core\Filament\Resources\Modules\Pages\EditModule;
use Modules\Core\Filament\Resources\Modules\Pages\ListModules;
use Modules\Core\Filament\Resources\Modules\Pages\ViewModule;
use Modules\Core\Filament\Resources\Modules\RelationManagers\TenantModulesRelationManager;
use Modules\Core\Filament\Resources\Modules\RelationManagers\TenantsRelationManager;
use Modules\Core\Filament\Resources\Modules\Schemas\ModuleForm;
use Modules\Core\Filament\Resources\Modules\Schemas\ModuleInfolist;
use Modules\Core\Filament\Resources\Modules\Tables\ModulesTable;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Core\Models\Module;

class ModuleResource extends LocalizedResource
{
    protected static ?string $model = Module::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ModuleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ModuleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TenantModulesRelationManager::class,
            TenantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
            'create' => CreateModule::route('/create'),
            'view' => ViewModule::route('/{record}'),
            'edit' => EditModule::route('/{record}/edit'),
        ];
    }
}
