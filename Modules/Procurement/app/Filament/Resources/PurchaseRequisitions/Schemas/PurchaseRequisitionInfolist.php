<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PurchaseRequisitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('User'))
                    ->placeholder('-'),
                TextEntry::make('requested_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('requested_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('request_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_number')),
                TextEntry::make('request_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_date'))
                    ->date(),
                TextEntry::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('priority')
                    ->label(\Modules\Core\Support\FilamentUi::field('priority')),
                TextEntry::make('justification')
                    ->label(\Modules\Core\Support\FilamentUi::field('justification'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('total_items')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_items'))
                    ->numeric(),
                TextEntry::make('total_estimated_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_amount'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
                    ->dateTime()
                    ->placeholder('-'),
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
