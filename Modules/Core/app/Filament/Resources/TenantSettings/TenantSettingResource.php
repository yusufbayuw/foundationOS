<?php

namespace Modules\Core\Filament\Resources\TenantSettings;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantSettings\Pages\CreateTenantSetting;
use Modules\Core\Filament\Resources\TenantSettings\Pages\EditTenantSetting;
use Modules\Core\Filament\Resources\TenantSettings\Pages\ListTenantSettings;
use Modules\Core\Filament\Resources\TenantSettings\Pages\ViewTenantSetting;
use Modules\Core\Filament\Resources\TenantSettings\Schemas\TenantSettingForm;
use Modules\Core\Filament\Resources\TenantSettings\Schemas\TenantSettingInfolist;
use Modules\Core\Filament\Resources\TenantSettings\Tables\TenantSettingsTable;
use Modules\Core\Models\TenantSetting;

class TenantSettingResource extends LocalizedResource
{
    protected static ?string $model = TenantSetting::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantSettingsTable::configure($table);
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
            'index' => ListTenantSettings::route('/'),
            'create' => CreateTenantSetting::route('/create'),
            'view' => ViewTenantSetting::route('/{record}'),
            'edit' => EditTenantSetting::route('/{record}/edit'),
        ];
    }
}
