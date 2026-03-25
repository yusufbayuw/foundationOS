<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseRequisitionItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('purchase_requisition_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_requisition_id'))
                    ->numeric(),
                TextEntry::make('procurementItem.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Procurement item'))
                    ->placeholder('-'),
                TextEntry::make('preferredVendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Preferred vendor'))
                    ->placeholder('-'),
                TextEntry::make('department.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Department'))
                    ->placeholder('-'),
                TextEntry::make('purchaseOrder.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase order'))
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('specifications')
                    ->label(\Modules\Core\Support\FilamentUi::field('specifications'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('quantity_requested')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity_requested'))
                    ->numeric(),
                TextEntry::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->placeholder('-'),
                TextEntry::make('estimated_unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_unit_price'))
                    ->money(),
                TextEntry::make('estimated_total_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_total_price'))
                    ->money(),
                TextEntry::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('usage_purpose')
                    ->label(\Modules\Core\Support\FilamentUi::field('usage_purpose'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('budgetAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Budget account'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('ordered_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('ordered_quantity'))
                    ->numeric(),
                TextEntry::make('received_quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('received_quantity'))
                    ->numeric(),
                TextEntry::make('rejection_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('rejection_reason'))
                    ->placeholder('-')
                    ->columnSpanFull(),
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
