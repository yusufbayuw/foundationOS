<?php

namespace Modules\Core\Filament\Resources\TenantModules;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantModules\Pages\CreateTenantModule;
use Modules\Core\Filament\Resources\TenantModules\Pages\EditTenantModule;
use Modules\Core\Filament\Resources\TenantModules\Pages\ListTenantModules;
use Modules\Core\Filament\Resources\TenantModules\Pages\ViewTenantModule;
use Modules\Core\Filament\Resources\TenantModules\Schemas\TenantModuleForm;
use Modules\Core\Filament\Resources\TenantModules\Schemas\TenantModuleInfolist;
use Modules\Core\Filament\Resources\TenantModules\Tables\TenantModulesTable;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Core\Models\TenantModule;

class TenantModuleResource extends LocalizedResource
{
    protected static ?string $model = TenantModule::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantModuleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantModuleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantModulesTable::configure($table);
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
            'index' => ListTenantModules::route('/'),
            'create' => CreateTenantModule::route('/create'),
            'view' => ViewTenantModule::route('/{record}'),
            'edit' => EditTenantModule::route('/{record}/edit'),
        ];
    }
}
