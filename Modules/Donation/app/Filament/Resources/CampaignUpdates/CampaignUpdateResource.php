<?php

namespace Modules\Donation\Filament\Resources\CampaignUpdates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\CampaignUpdates\Pages\CreateCampaignUpdate;
use Modules\Donation\Filament\Resources\CampaignUpdates\Pages\EditCampaignUpdate;
use Modules\Donation\Filament\Resources\CampaignUpdates\Pages\ListCampaignUpdates;
use Modules\Donation\Filament\Resources\CampaignUpdates\Pages\ViewCampaignUpdate;
use Modules\Donation\Filament\Resources\CampaignUpdates\Schemas\CampaignUpdateForm;
use Modules\Donation\Filament\Resources\CampaignUpdates\Schemas\CampaignUpdateInfolist;
use Modules\Donation\Filament\Resources\CampaignUpdates\Tables\CampaignUpdatesTable;
use Modules\Donation\Models\CampaignUpdate;

class CampaignUpdateResource extends ModuleResource
{
    protected static ?string $model = CampaignUpdate::class;

    public static function form(Schema $schema): Schema
    {
        return CampaignUpdateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignUpdateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignUpdatesTable::configure($table);
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
            'index' => ListCampaignUpdates::route('/'),
            'create' => CreateCampaignUpdate::route('/create'),
            'view' => ViewCampaignUpdate::route('/{record}'),
            'edit' => EditCampaignUpdate::route('/{record}/edit'),
        ];
    }
}
