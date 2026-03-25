<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubscriptionPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price_monthly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_monthly'))
                    ->numeric(),
                TextEntry::make('price_yearly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_yearly'))
                    ->numeric(),
                TextEntry::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->numeric(),
                TextEntry::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->numeric(),
                TextEntry::make('max_storage_gb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_gb'))
                    ->numeric(),
                TextEntry::make('included_modules')
                    ->label(\Modules\Core\Support\FilamentUi::field('included_modules'))
                    ->columnSpanFull(),
                TextEntry::make('features')
                    ->label(\Modules\Core\Support\FilamentUi::field('features'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_recommended')
                    ->boolean(),
                TextEntry::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
