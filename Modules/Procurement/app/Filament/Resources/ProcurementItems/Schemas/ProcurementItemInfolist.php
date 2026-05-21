<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ProcurementItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('category.name')
                            ->label(FilamentUi::text('Category'))
                            ->placeholder('-'),
                        TextEntry::make('preferredVendor.name')
                            ->label(FilamentUi::text('Preferred vendor'))
                            ->placeholder('-'),
                        TextEntry::make('chartOfAccount.name')
                            ->label(FilamentUi::text('Chart of account'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & Ordering')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('unit_of_measure')
                            ->label(FilamentUi::field('unit_of_measure'))
                            ->placeholder('-'),
                        TextEntry::make('estimated_price')
                            ->label(FilamentUi::field('estimated_price'))
                            ->money()
                            ->placeholder('-'),
                        TextEntry::make('last_purchase_price')
                            ->label(FilamentUi::field('last_purchase_price'))
                            ->money()
                            ->placeholder('-'),
                        TextEntry::make('minimum_order_quantity')
                            ->label(FilamentUi::field('minimum_order_quantity'))
                            ->numeric(),
                        TextEntry::make('lead_time_days')
                            ->label(FilamentUi::field('lead_time_days'))
                            ->numeric(),
                        TextEntry::make('specifications')
                            ->label(FilamentUi::field('specifications'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Timestamps')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
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
