<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\FoundationProfiles\Pages\CreateFoundationProfile;
use Modules\Core\Filament\Resources\FoundationProfiles\Pages\EditFoundationProfile;
use Modules\Core\Filament\Resources\FoundationProfiles\Pages\ListFoundationProfiles;
use Modules\Core\Filament\Resources\FoundationProfiles\Pages\ViewFoundationProfile;
use Modules\Core\Filament\Resources\FoundationProfiles\Schemas\FoundationProfileForm;
use Modules\Core\Filament\Resources\FoundationProfiles\Schemas\FoundationProfileInfolist;
use Modules\Core\Filament\Resources\FoundationProfiles\Tables\FoundationProfilesTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\FoundationProfile;

class FoundationProfileResource extends ModuleResource
{
    protected static ?string $model = FoundationProfile::class;

    public static function form(Schema $schema): Schema
    {
        return FoundationProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FoundationProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoundationProfilesTable::configure($table);
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
            'index' => ListFoundationProfiles::route('/'),
            'create' => CreateFoundationProfile::route('/create'),
            'view' => ViewFoundationProfile::route('/{record}'),
            'edit' => EditFoundationProfile::route('/{record}/edit'),
        ];
    }
}
