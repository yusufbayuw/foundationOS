<?php

namespace Modules\Enrollment\Filament\Resources\MarketingCampaigns;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages\CreateMarketingCampaign;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages\EditMarketingCampaign;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages\ListMarketingCampaigns;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Pages\ViewMarketingCampaign;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Schemas\MarketingCampaignForm;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Schemas\MarketingCampaignInfolist;
use Modules\Enrollment\Filament\Resources\MarketingCampaigns\Tables\MarketingCampaignsTable;
use Modules\Enrollment\Models\MarketingCampaign;

class MarketingCampaignResource extends ModuleResource
{
    protected static ?string $model = MarketingCampaign::class;

    public static function form(Schema $schema): Schema
    {
        return MarketingCampaignForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketingCampaignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketingCampaignsTable::configure($table);
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
            'index' => ListMarketingCampaigns::route('/'),
            'create' => CreateMarketingCampaign::route('/create'),
            'view' => ViewMarketingCampaign::route('/{record}'),
            'edit' => EditMarketingCampaign::route('/{record}/edit'),
        ];
    }
}
