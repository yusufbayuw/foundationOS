<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseOrderItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order')),
                TextEntry::make('purchaseRequisitionItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase requisition item'))
                    ->placeholder('-'),
                TextEntry::make('procurementItem.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Procurement item'))
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->numeric(),
                TextEntry::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->placeholder('-'),
                TextEntry::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money(),
                TextEntry::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric(),
                TextEntry::make('tax_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_percentage'))
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_amount'))
                    ->numeric(),
                TextEntry::make('line_total')
                    ->label(\Modules\Core\Support\FilamentUi::field('line_total'))
                    ->numeric(),
                TextEntry::make('quantity_received')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_received'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
