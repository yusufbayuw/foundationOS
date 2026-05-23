<?php

namespace Modules\Donation\Filament\Resources\Campaigns;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\Campaigns\Pages\CreateCampaign;
use Modules\Donation\Filament\Resources\Campaigns\Pages\EditCampaign;
use Modules\Donation\Filament\Resources\Campaigns\Pages\ListCampaigns;
use Modules\Donation\Filament\Resources\Campaigns\Pages\ViewCampaign;
use Modules\Donation\Filament\Resources\Campaigns\Schemas\CampaignForm;
use Modules\Donation\Filament\Resources\Campaigns\Tables\CampaignsTable;
use Modules\Donation\Models\Campaign;

class CampaignResource extends ModuleResource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}
