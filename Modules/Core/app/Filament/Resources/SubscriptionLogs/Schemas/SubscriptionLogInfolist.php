<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubscriptionLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action')),
                TextEntry::make('previousPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Previous plan'))
                    ->placeholder('-'),
                TextEntry::make('newPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('New plan'))
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method'))
                    ->placeholder('-'),
                TextEntry::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->placeholder('-'),
                TextEntry::make('payment_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_proof'))
                    ->placeholder('-'),
                TextEntry::make('invoice_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_number'))
                    ->placeholder('-'),
                TextEntry::make('invoice_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_url'))
                    ->placeholder('-'),
                TextEntry::make('period_start')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_start'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('period_end')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_end'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('metadata')
                    ->label(\Modules\Core\Support\FilamentUi::field('metadata'))
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
