<?php

namespace Modules\Enrollment\Filament\Resources\MarketingCampaigns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class MarketingCampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('spend_amount')
                    ->numeric(),
                TextEntry::make('starts_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('ends_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
