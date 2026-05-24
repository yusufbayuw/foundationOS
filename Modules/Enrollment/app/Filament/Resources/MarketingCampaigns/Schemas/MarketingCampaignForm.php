<?php

namespace Modules\Enrollment\Filament\Resources\MarketingCampaigns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MarketingCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('spend_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('starts_on'),
                DatePicker::make('ends_on'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
