<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProcurementItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('category.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Category'))
                    ->placeholder('-'),
                TextEntry::make('preferredVendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Preferred vendor'))
                    ->placeholder('-'),
                TextEntry::make('chartOfAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Chart of account'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->placeholder('-'),
                TextEntry::make('estimated_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_price'))
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('last_purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_purchase_price'))
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('minimum_order_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('minimum_order_quantity'))
                    ->numeric(),
                TextEntry::make('lead_time_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('lead_time_days'))
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
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
