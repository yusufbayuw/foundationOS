<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SubscriptionPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('price_monthly')
                            ->label(FilamentUi::field('price_monthly'))
                            ->numeric(),
                        TextEntry::make('price_yearly')
                            ->label(FilamentUi::field('price_yearly'))
                            ->numeric(),
                    ]),

                Section::make('Limits')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('max_users')
                            ->label(FilamentUi::field('max_users'))
                            ->numeric(),
                        TextEntry::make('max_organizations')
                            ->label(FilamentUi::field('max_organizations'))
                            ->numeric(),
                        TextEntry::make('max_storage_gb')
                            ->label(FilamentUi::field('max_storage_gb'))
                            ->numeric(),
                    ]),

                Section::make('Features')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('included_modules')
                            ->label(FilamentUi::field('included_modules'))
                            ->columnSpanFull(),
                        TextEntry::make('features')
                            ->label(FilamentUi::field('features'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
                        IconEntry::make('is_recommended')
                            ->boolean(),
                        TextEntry::make('display_order')
                            ->label(FilamentUi::field('display_order'))
                            ->numeric(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
