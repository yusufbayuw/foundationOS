<?php

namespace Modules\Sales\Filament\Resources\SalesOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SalesOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->placeholder('-'),
                    TextEntry::make('customer_id')
                        ->label(FilamentUi::field('customer_id'))
                        ->placeholder('-'),
                    TextEntry::make('order_number')
                        ->label(FilamentUi::field('order_number'))
                        ->placeholder('-'),
                    TextEntry::make('order_date')
                        ->label(FilamentUi::field('order_date'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('subtotal')
                        ->label(FilamentUi::field('subtotal'))
                        ->placeholder('-'),
                    TextEntry::make('tax_amount')
                        ->label(FilamentUi::field('tax_amount'))
                        ->placeholder('-'),
                    TextEntry::make('total_amount')
                        ->label(FilamentUi::field('total_amount'))
                        ->placeholder('-'),
                    TextEntry::make('confirmed_at')
                        ->label(FilamentUi::field('confirmed_at'))
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
