<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RequestForQuotationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('purchaseRequisition.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Purchase requisition')),
                TextEntry::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rfq_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_number')),
                TextEntry::make('rfq_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_date'))
                    ->date(),
                TextEntry::make('closing_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('closing_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('total_estimated_budget')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_budget'))
                    ->numeric(),
                TextEntry::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency')),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('award_criteria')
                    ->label(\Modules\Core\Support\FilamentUi::field('award_criteria'))
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
