<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\OrganizationSettings\Pages\CreateOrganizationSetting;
use Modules\Core\Filament\Resources\OrganizationSettings\Pages\EditOrganizationSetting;
use Modules\Core\Filament\Resources\OrganizationSettings\Pages\ListOrganizationSettings;
use Modules\Core\Filament\Resources\OrganizationSettings\Pages\ViewOrganizationSetting;
use Modules\Core\Filament\Resources\OrganizationSettings\Schemas\OrganizationSettingForm;
use Modules\Core\Filament\Resources\OrganizationSettings\Schemas\OrganizationSettingInfolist;
use Modules\Core\Filament\Resources\OrganizationSettings\Tables\OrganizationSettingsTable;
use Modules\Core\Models\OrganizationSetting;

class OrganizationSettingResource extends LocalizedResource
{
    protected static ?string $model = OrganizationSetting::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrganizationSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationSettingsTable::configure($table);
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
            'index' => ListOrganizationSettings::route('/'),
            'create' => CreateOrganizationSetting::route('/create'),
            'view' => ViewOrganizationSetting::route('/{record}'),
            'edit' => EditOrganizationSetting::route('/{record}/edit'),
        ];
    }
}
