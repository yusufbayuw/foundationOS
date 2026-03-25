<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RfqVendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('requestForQuotation.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Request for quotation')),
                TextEntry::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Vendor')),
                TextEntry::make('invitation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('invitation_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('response_deadline')
                    ->label(\Modules\Core\Support\FilamentUi::field('response_deadline'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('responded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('responded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('quotation_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_amount'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('quotation_document')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_document'))
                    ->placeholder('-'),
                TextEntry::make('technical_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('technical_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('price_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_shortlisted')
                    ->boolean(),
                IconEntry::make('is_awarded')
                    ->boolean(),
                TextEntry::make('award_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('award_reason'))
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
